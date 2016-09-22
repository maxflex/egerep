<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    public $loggable   = false;
    public $timestamps = false;

    const TEST_NUMBER		= '74955653170';
    const EGEREP_NUMBER 	= '74956461080';
    const EGECENTR_NUMBER 	= '74956468592';

    /*
        Алгоритм: сначала получаем все сегодняшние пропущенные, затем исключаем по условиям
            * WHERE mango.start > missed.start									дата звонка > даты пропущенного звонка
            * mango.to_number = missed.from_number								мы перезвонили (неважно ответил ли клиент)
            * mango.from_number = missed.from_number and mango.answer != 0		клиент сам перезвонил и мы ответили
    */

    private static function getMissedCallsSql()
    {
        return "
                    FROM (
                        SELECT entry_id, from_number, start
                        FROM `mango`
                        WHERE DATE(NOW()) = DATE(FROM_UNIXTIME(start)) and from_extension=0 "
                        . (! empty($excluded = \Cache::get("excluded_missed")) ? " and entry_id not in (" . implode(",", array_map('wrapString', $excluded)) . ") " : "") . "
                        GROUP BY entry_id
                        HAVING sum(answer) = 0
                    ) missed 
                    WHERE NOT EXISTS (SELECT 1 FROM mango WHERE mango.start > missed.start and 
                        (mango.to_number = missed.from_number or (mango.from_number = missed.from_number and mango.answer != 0))
                    )
                    GROUP BY from_number 
                    ORDER BY start DESC";
    }

    /**
     * Выбираем пропущенные за сегодня звонки, на которые потом не перезвонили
     */
    public static function missed($get_caller = true)
    {
        $missed = \DB::select(\DB::raw("SELECT * " . self::getMissedCallsSql()));
        foreach($missed as &$call) {
            if ($get_caller) {
                $call->caller = self::getCaller($call->from_number);
            }
        }
        return $missed;
    }

    /*
		 * Определить звонящего
		 */
    public static function getCaller($phone)
    {
        // Ищем учителя с таким номером
        $tutor = \DB::select("
            	select id, first_name, last_name, middle_name from tutors
            	WHERE phone='{$phone}' OR phone2='{$phone}' OR phone3='{$phone}'  OR phone4='{$phone}'
            ");
        if (!empty($tutor)) {
            $tutor[0]->type = 'tutor';
            return $tutor[0];
        } else {
            # ищем ученика в ЕГЭ-РЕПЕТИТОРЕ с таким номером
            $client = \DB::select("
                    select id, name from clients  
                    WHERE phone='{$phone}' OR phone2='{$phone}' OR phone3='{$phone}' OR phone4='{$phone}'
                ");
            // Если заявка с таким номером телефона уже есть, подхватываем ученика оттуда
            if (!empty($client)) {
                $client[0]->type = 'client';
                return $client[0];
            }
        }

        // возвращается, если номера нет в базе
        if (! isset($return)) {
            $return = ['type' => false];
        }

        return $return;
    }

    /*
     * Кол-во пропущенных сегдоня звонков, на которые не ответили
     */
    public static function countMissed()
    {
        return count(\DB::select("SELECT 1 " . self::getMissedCallsSql()));
    }

    public static function excludeFromMissed($entry_id)
    {
        $excluded = \Cache::remember('excluded_missed', minutesTillNextDay(), function() { return []; });
        $excluded[] = $entry_id;

        \Cache::put('excluded_missed', $excluded, minutesTillNextDay());
    }
}




