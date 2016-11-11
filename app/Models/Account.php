<?php

namespace App\Models;

use App\Events\DebtRecalc;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccountData;
use App\Models\Tutor;
use App\Models\User;
use Carbon\Carbon;
use DB;

class Account extends Model
{
    // комиссия по умолчанию в процентах
    const DEFAULT_COMMISSION = 0.25;
    // id status a взаимозачетов в таблице payments в NEC
    const MUTUAL_DEBT_STATUS = 6;

    protected $fillable = [
        'date_end',
        'tutor_id',
        'received',
        'user_id',
        'debt',
        'debt_type',
        'comment',
        'payment_method',
        'data',
        'confirmed',
    ];
    protected $appends = ['data', 'user_login', 'mutual_debts'];

    // ------------------------------------------------------------------------

    public function tutor()
    {
        return $this->belongsTo('App\Models\Tutor');
    }

    /**
     * Данные по отчетности
     */
    public function accountData()
    {
        return $this->hasMany('App\Models\AccountData', 'tutor_id', 'tutor_id')
        //    ->whereRaw("date > DATE_SUB((SELECT date_end FROM accounts WHERE tutor_id=" . $this->tutor_id . " ORDER BY date_end DESC LIMIT 1), INTERVAL 60 DAY)")
            ->where('date', '>', $this->date_start)
            ->where('date', '<=', $this->date_end);
    }

    // ------------------------------------------------------------------------

    public function getUserLoginAttribute()
    {
        return User::where('id', $this->user_id)->pluck('login')->first();
    }

    public function getDataAttribute()
    {
        // обязательно возвращать пустой объект, если данные пусты,
        // иначе на фронт-энде вернется пустой массив и будут проблемы
        if (! count($this->accountData)) {
            return emptyObject();
        }

        foreach ($this->accountData as $d) {
            $return[$d->client_id][$d->date] = $d->value;
        }
        return $return;
    }

    public function setDataAttribute($value)
    {
        foreach ($value as $client_id => $data) {
            foreach ($data as $date => $value) {
                AccountData::updateOrCreate([
                    'client_id' => $client_id,
                    'tutor_id'  => $this->tutor_id,
                    'date'      => $date,
                ], ['value' => $value]);
            }
        }
    }

    /**
     * Получить дату начала встречи (= конец предыдущей встречи или дата самой ранней стыковки)
     */
    public function getDateStartAttribute()
    {
        $date_start = static::where('date_end', '<', $this->date_end)
                            ->where('tutor_id', $this->tutor_id)
                            ->orderBy('date_end', 'desc')
                            ->pluck('date_end')
                            ->first();

        // если предыдущей встречи нет, то дата самой ранней стыковки
        if ($date_start === null) {
            // к дате нужно прибавить -1, чтобы входило в $this->data
            $date_start = Carbon::createFromFormat('Y-m-d', $this->tutor->getFirstAttachmentDate())->subDay();
        }

        return date('Y-m-d', strtotime($date_start));
    }

    /**
     * Итого комиссия за период
     */
    public function getTotalCommissionAttribute()
    {
        $total_commission = 0;

        if (count($this->accountData)) {
            foreach ($this->accountData as $data) {
                if ($data->commission) {
                    $total_commission += $data->commission;
                } else {
                    $total_commission += round($data->sum * static::DEFAULT_COMMISSION);
                }
            }
        }

        return $total_commission;
    }

    public function getMutualDebtsAttribute()
    {
        return DB::connection('egecrm')
                 ->table('payments')
                 ->select('sum')
                 ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') = '{$this->date_end}'")
                 ->where('entity_id', $this->tutor_id)
                 ->where('entity_type', Tutor::USER_TYPE)
                 ->where('id_status', static::MUTUAL_DEBT_STATUS)->first();
    }
    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::saving(function($model) {
            if (! $model->exists) {
                $model->user_id = User::fromSession()->id;
            }
        });

        static::deleting(function ($model) {
            // Delete account data
            $model->accountData()->delete();
        });

        static::created(function ($model) {
            event(new DebtRecalc($model->tutor_id));
        });

        static::deleted(function ($model) {
            event(new DebtRecalc($model->tutor_id));
        });
    }

    public function save(array $options = [])
    {
        $fire_event = $this->exists && $this->changed(['date_end']);

        parent::save($options);

        if ($fire_event) {
            event(new DebtRecalc($this->tutor_id));
        }
    }

    /**
     * количество элементов пагинации в странице детализации итогов.
     */
    public static function summaryItemsCount($filter = 'day')
    {
        $first_date = new \DateTime(static::orderBy('date_end')->pluck('date_end')->first());

        switch ($filter) {
            case 'day':
                return $first_date->diff(new \DateTime)->format('%a');
            case 'week':
                return intval($first_date->diff(new \DateTime)->format('%a') / 7);
            case 'month':
                return ((new \DateTime)->format('Y') -  $first_date->format('Y'))*12 + $first_date->diff(new \DateTime)->format('%m') + 1;
            case 'year':
                $cnt = (new \DateTime)->format('Y') - $first_date->format('Y');
                $cnt += (new \DateTime)->format('m') < 7
                    ? $first_date->format('m') < 7
                        ? 1
                        : 0
                    : $first_date->format('m') < 7
                        ? 2
                        : 1;
                return $cnt;
        }
    }
}
