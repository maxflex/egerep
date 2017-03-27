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
            where true "
                . (isset($params['debtor']) ? " and debtor=" . $params['debtor'] : '')
                . (isset($params['after_last_meeting']) ? " and after_last_meeting=" . $params['after_last_meeting'] : '')
                . (isset($params['date_start']) ? " and date>='" . $params['date_start'] . "'" : '')
                . (isset($params['date_end'])   ? " and date<='" . $params['date_end'] . "'" : '')
                . (isset($params['tutor_id'])   ? " and tutor_id='" . $params['tutor_id'] . "'" : '')
        )[0]->sum;
    }
}
