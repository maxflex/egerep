<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AccountData;
use App\Models\Tutor;
use App\Models\User;
use App\Events\RecalcTutorDebt;
use Carbon\Carbon;
use DB;

class Account extends Model
{
    // комиссия по умолчанию в процентах
    const DEFAULT_COMMISSION = 0.25;

    protected $fillable = [
        'date_end',
        'tutor_id',
        'user_id',
        'debt',
        'debt_type',
        'payment_method',
        'data',
        'confirmed',
    ];
    protected $appends = ['data', 'user_login', 'debt_calc', 'all_payments'];

    // ------------------------------------------------------------------------

    public function tutor()
    {
        return $this->belongsTo('App\Models\Tutor');
    }

    public function payments()
    {
        return $this->hasMany(AccountPayment::class);
    }

    /**
     * Данные по отчетности
     */
    public function accountData()
    {
        // attachment-refactored
        return $this->hasManyThroughCustom(AccountData::class, Attachment::class, 'id', 'attachment_id')
            ->where('attachments.tutor_id', $this->tutor_id)
            ->where('account_datas.date', '>', $this->date_start)
            ->where('account_datas.date', '<=', $this->date_end);
    }

    // ------------------------------------------------------------------------

    public function getUserLoginAttribute()
    {
        return User::where('id', $this->user_id)->pluck('nickname')->first();
    }

    public function getDataAttribute()
    {
        // обязательно возвращать пустой объект, если данные пусты,
        // иначе на фронт-энде вернется пустой массив и будут проблемы
        if (! count($this->accountData)) {
            return emptyObject();
        }

        foreach ($this->accountData as $d) {
            // attachment-refactored
            $return[$d->id][$d->date] = $d->value;
        }
        return $return;
    }

    public function setDataAttribute($value)
    {
        // attachment-refactored
        foreach ($value as $attachment_id => $data) {
            foreach ($data as $date => $value) {
                AccountData::updateOrCreate([
                    'attachment_id' => $attachment_id,
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
     * Все платежи (локальные + взаимозачеты)
     */
    public function getAllPaymentsAttribute()
    {
        return $this->payments->all();
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

    public function getDebtCalcAttribute()
    {
        return round(Debt::sum([
            'tutor_id' => $this->tutor_id,
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
        ]));
    }

    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::saving(function($model) {
            if (! $model->exists) {
                $model->user_id = User::id();
            }
        });

        static::deleting(function ($model) {
            // Delete account data
            $model->accountData()->delete();
        });
        static::updated(function($model) {
            if ($model->changed(['date_end'])) {
                // запускаем только если это последний расчет
                if ($model->id == DB::table('accounts')->where('tutor_id', $model->tutor_id)->orderBy('date_end', 'desc')->value('id')) {
                    event(new RecalcTutorDebt($model->tutor_id));
                }
            }
        });
        static::saved(function($model) {
            DB::table('accounts')->where('id', $model->id)->update(['errors' => \App\Models\Helpers\Account::errors($model)]);
        });
        static::created(function ($model) {
            event(new RecalcTutorDebt($model->tutor_id));
        });
        static::deleted(function ($model) {
            event(new RecalcTutorDebt($model->tutor_id));
        });
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

    public static function search($request)
    {
        $query = static::query();

        if (isset($request->confirmed)) {
            $query->where('confirmed', $request->confirmed);
        }

        if (isset($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        if (isset($request->error) && $request->error) {
            $query->whereRaw("FIND_IN_SET({$request->error}, errors)");
        }

        return $query;
    }
}
