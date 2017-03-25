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
            select sum(debts.debt) as `sum` from tutors
            left join (
            	select tutor_id, max(date_end) as `date` from accounts
            	group by tutor_id
            ) a on a.tutor_id = tutors.id
            left join debts on (debts.tutor_id = tutors.id and (a.tutor_id is null or a.date >= debts.date))
            where tutors.debtor=0"
                . (isset($params['date_start']) ? " and debts.date>='" . $params['date_start'] . "'" : '')
                . (isset($params['date_end'])   ? " and debts.date<='" . $params['date_end'] . "'" : '')
                . (isset($params['tutor_id'])   ? " and tutors.id='" . $params['tutor_id'] . "'" : '')
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
