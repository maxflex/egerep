<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AccountData;
use App\Models\Tutor;
use Carbon\Carbon;
use DB;

class Account extends Model
{
    // комиссия по умолчанию в процентах
    const DEFAULT_COMMISSION = 0.25;

    protected $fillable = [
        'date_end',
        'tutor_id',
        'received',
        'debt',
        'debt_type',
        'debt_before',
        'comment',
        'payment_method',
        'data',
    ];
    protected $appends = ['data', 'user_login'];

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

    // ------------------------------------------------------------------------

    /**
     * Пересчитать расчетный дебет
     *
     * $last_debt – поседний период ... сегодня (хранится в таблице преподавателей)
     */
    public function recalcDebt($date_start, $date_end, $last_debt = false)
    {
        // Получить клиентов, соответствующих периоду
        $data = DB::table('attachments')->leftJoin('archives', 'archives.attachment_id', '=', 'attachments.id')
            ->where('attachments.tutor_id', $this->tutor_id)
            ->where('attachments.date', '<=', $date_end)
            ->where(function($query) use ($date_start) {
                $query->where('archives.id', null)
                      ->orWhere('archives.date', '>', $date_start); // может >= ?
            })
            ->select(DB::raw('
                attachments.forecast,
                attachments.client_id,
                attachments.date as attachment_date,
                archives.date as archive_date'))
            ->get();

        // Пересчитываем
        $debt = 0;

        foreach (dateRange($date_start, $date_end) as $date) {
            $coef = static::_pissimisticCoef($date);
            foreach ($data as $d) {
                if (($d->attachment_date <= $date) && ($d->archive_date >= $date)) {
                    $debt += ($d->forecast / 7) * $coef;
                }
            }
        }

        if ($last_debt) {
            Tutor::where('id', $this->tutor_id)->update([
                'debt_calc'     => $debt,
                'debt_updated'  => now(),
            ]);
        } else {
            Account::where('id', $this->id)->update([
                'debt_calc' => $debt
            ]);
            // если это последний период, то обновить в промежутке
            // поседний период ... сегодня
            if ($this->id == static::where('tutor_id', $this->tutor_id)->take(1)->orderBy('date_end', 'desc')->value('id')) {
                // @todo: нужно проверить ситуацию, когда конец периода = сегодня
                $this->recalcDebt($this->date_end, now(true), true);
            }
        }
    }

    /**
     * Писсимизирующий коэффициент
     */
    private static function _pissimisticCoef($date)
    {
        $date = date('m-d', strtotime($date));

        // первые 7 дней ноября
        if ($date >= '11-01' && $date <= '11-07') {
            return .6;
        }
        // последние 7 дней декабря 0,54
        if ($date >= '12-25' && $date <= '12-31') {
            return .54;
        }
        // первые 10 дней января - 0,12
        if ($date >= '01-01' && $date <= '01-10') {
            return .12;
        }
        // первые 14 дней мая - 0,54
        if ($date >= '05-01' && $date <= '05-14') {
            return .54;
        }
        // с 1 июня по 20 августа - 0,02 (или 0,72 если была архивации в летный период)
        if ($date >= '06-01' && $date <= '08-20') {
            return .02;
        }
        // последние 7 дней декабря 0,54
        if ($date >= '12-25' && $date <= '12-31') {
            return .54;
        }
        // с 21 августа по 31 августа - 0,1 (или 0,72 если была архивации в летный период)
        if ($date >= '08-21' && $date <= '08-31') {
            return .1;
        }
        // остальные дни - 0,72
        return .72;
    }

    protected static function boot()
    {
        static::deleting(function ($account) {
            // Delete account data
            $account->accountData()->delete();
        });
    }
}
