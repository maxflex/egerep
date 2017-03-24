<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class Debt extends Model
{
    public $timestamps = false;

    /**
     * Посчитать дебет преподавателя
     * $date_start/$date_end – сколько должен на текущий момент времени
     */
    public static function tutor($tutor_id, $date_start = null, $date_end = null)
    {
        # получаем последнюю встречу
        $last_account_date = Cache::remember("tutor:{$tutor_id}:last_account_date", 5, function() use ($tutor_id) {
            return Account::where('tutor_id', $tutor_id)->orderBy('date_end', 'desc')->value('date_end');
        });

        # суммируем дебет с даты последней встречи
        # или весь дебет, если встреч не было
        $query = Debt::where('tutor_id', $tutor_id);
        if ($last_account_date !== null) {
            $query->where('date', '>=', $last_account_date);
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
        // \Log::info("{$date_start} – {$date_end}");
        $tutor_ids = Cache::remember('debt_tutor_ids', 60, function() {
            return self::join('tutors', 'tutors.id', '=', 'debts.tutor_id')
                ->where('tutors.debtor', 0)->groupBy('debts.tutor_id')->pluck('debts.tutor_id');
        });

        $sum = 0;

        foreach ($tutor_ids as $tutor_id) {
            $sum += Debt::tutor($tutor_id, $date_start, $date_end);
        }

        return $sum;
    }

    public static function sum($date_start, $date_end)
    {
        $tutor_ids = Cache::remember('debt_tutor_ids', 60, function() {
            return self::join('tutors', 'tutors.id', '=', 'debts.tutor_id')
                ->where('tutors.debtor', 0)->groupBy('debts.tutor_id')->pluck('debts.tutor_id');
        });

        return self::whereIn('tutor_id', $tutor_ids)->where('date', '>=', $date_start)->where('date', '<=', $date_end)->sum('debt');
    }
}
