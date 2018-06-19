<?php

namespace App\Models\Helpers;
use Illuminate\Database\Eloquent\Model;
use DB;

class MutualPayment extends Model
{
    // id status a взаимозачетов в таблице payments в NEC
    const MUTUAL_PAYMENT_STATUS = 6;

    /**
     * query для взаимозачетов
     */
    public static function query()
    {
        return dbEgecrm('payments')->where('entity_type', 'TEACHER')->where('id_status', self::MUTUAL_PAYMENT_STATUS);
    }

    public static function betweenDates($date_start, $date_end)
    {
        return self::query()->whereRaw("date >= '{$date_start}'")->whereRaw("date <= '{$date_end}'");
    }

    public static function defaultSelect()
    {
        return DB::raw('entity_id as tutor_id, id_user as user_id, sum, first_save_date as created_at, `date`, confirmed');
    }
}
