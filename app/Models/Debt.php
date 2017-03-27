<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;
use DB;

class Debt extends Model
{
    public $timestamps = false;

    /**
     * Посчитать общий дебет
     * $params | date_start | date_end | tutor_id
     */
    public static function sum($params = [])
    {
        return DB::select("
            select sum(debt) as `sum` from debts
            where debtor=0 and after_last_meeting=1"
                . (isset($params['date_start']) ? " and date>='" . $params['date_start'] . "'" : '')
                . (isset($params['date_end'])   ? " and date<='" . $params['date_end'] . "'" : '')
                . (isset($params['tutor_id'])   ? " and tutor_id='" . $params['tutor_id'] . "'" : '')
        )[0]->sum;
    }

    // tmp
    public static function sumNoAccounts($date_start, $date_end)
    {
        $tutor_ids = Cache::remember('debt_tutor_ids', 60, function() {
            return self::join('tutors', 'tutors.id', '=', 'debts.tutor_id')
                ->where('tutors.debtor', 0)->groupBy('debts.tutor_id')->pluck('debts.tutor_id');
        });

        return self::whereIn('tutor_id', $tutor_ids)->where('date', '>=', $date_start)->where('date', '<=', $date_end)->sum('debt');
    }
}
