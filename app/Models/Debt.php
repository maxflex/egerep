<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    public $timestamps = false;

    /**
     * Посчитать дебет преподавателя
     */
    public static function tutor($tutor_id)
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

        return $query->sum('debt');
    }

    /**
     * Посчитать общий дебет
     */
    public static function total()
    {
        $tutor_ids = self::where('debtor', 0)->groupBy('tutor_id')->pluck('tutor_id');

        $sum = 0;

        foreach ($tutor_ids as $tutor_id) {
            $sum += Debt::tutor($tutor_id);
        }

        return $sum;
    }
}
