<?php

namespace App\Models\Helpers;
use Illuminate\Database\Eloquent\Model;

class MutualPayment extends Model
{
    // id status a взаимозачетов в таблице payments в NEC
    const MUTUAL_PAYMENT_STATUS = 6;

    /**
     * query для взаимозачетов
     */
    public static function query()
    {
        return dbEgecrm('payments')->where('entity_type', \App\Models\Tutor::USER_TYPE)->where('id_status', self::MUTUAL_PAYMENT_STATUS);
    }

    public static function betweenDates($date_start, $date_end)
    {
        return self::query()->whereRaw("STR_TO_DATE(date, '%d.%m.%Y') >= '{$date_start}'")
                ->whereRaw("STR_TO_DATE(date, '%d.%m.%Y') <= '{$date_end}'");
    }
}
