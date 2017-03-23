<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    public $timestamps = false;

    /**
     * Посчитать дебет преподавателя
     */
    public static function tutor($tutor_id, $date_start = null, $date_end = null)
    {
        # получаем последнюю встречу
        $query = Account::where('tutor_id', $tutor_id);
        if ($query->exists()) {
            $last_account_date = $query->orderBy('date_end', 'desc')->value('date_end');
        }

        # суммируем дебет с даты последней встречи
        # или весь дебет, если встреч не было
        $query = Debt::where('tutor_id', $tutor_id);
        if (isset($last_account_date)) {
            $query->where('date', '>', $last_account_date);
        }

        # для статистики
        if ($date_start !== null) {
            $query->where('date', '>=', $date_start);
        }
        if ($date_end !== null) {
            $query->where('date', '<=', $date_end);
        }

        return $query->sum('debt');
    }

    /**
     * Посчитать общий дебет
     */
    public static function total($date_start = null, $date_end = null)
    {
        $tutor_ids = self::groupBy('tutor_id')->pluck('tutor_id');

        $sum = 0;

        foreach ($tutor_ids as $tutor_id) {
            // добавить join в получение tutor_ids
            if (! \DB::table('tutors')->whereId($tutor_id)->value('debtor')) {
                $sum += Debt::tutor($tutor_id, $date_start, $date_end);
            }
        }

        return $sum;
    }
}
