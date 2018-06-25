<?php

namespace App\Models\Service;

use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    public $loggable   = false;
    public $timestamps = false;

    const TEST_NUMBER		= '74955653170';
    const EGEREP_NUMBER 	= '74956461080';
    const EGECENTR_NUMBERS 	= ['74956468592', '79999999999'];

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
                        WHERE DATE(NOW()) = DATE(FROM_UNIXTIME(start)) and from_extension=0 and line_number=" . self::EGEREP_NUMBER
                        . (! empty($excluded = Redis::command('smembers', ['laravel:excluded_missed'])) ? " and entry_id not in (" . implode(",", array_map('wrapString', $excluded)) . ") " : "") . "
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
                $call->caller = self::getCaller($call->from_number, self::EGEREP_NUMBER);
            }
        }
        return $missed;
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
        Redis::command('sadd', ['laravel:excluded_missed', $entry_id]);
        Redis::command('expire', ['laravel:excluded_missed', secondsTillNextDay()]);
    }

    /*
     * Номер ЕГЭ-Центра
     */
    public static function isEgecentr($number) {
        return in_array($number, self::EGECENTR_NUMBERS);
    }

    /*
     * Номер ЕГЭ-Репетитора
     */
    public static function isEgerep($number) {
        return $number == self::EGEREP_NUMBER;
    }

    /*
     * Определить звонящего
     */
    public static function getCaller($phone, $to_number)
    {
        if (static::isEgerep($to_number)) {
            $return = static::determineEgerep($phone);
        }

        // возвращается, если номера нет в базе
        if (! is_object($return)) {
            $return = ['type' => false];
        }

        return $return;
    }

    /**
     * Определить номер для ЕГЭ-Репетитора
     */
    private static function determineEgerep($phone)
    {
        # ищем клиента в ЕГЭ-РЕПЕТИТОРЕ с таким номером
        $client = \DB::select("
                    select id, name from clients
                    WHERE phone='{$phone}' OR phone2='{$phone}' OR phone3='{$phone}' OR phone4='{$phone}'
                ");
        // Если заявка с таким номером телефона уже есть, подхватываем ученика оттуда
        if (!empty($client)) {
            $client[0]->type = 'client';
            return $client[0];
        }

        // Ищем учителя с таким номером
        $tutor = \DB::select("
            	select id, first_name, last_name, middle_name from tutors
            	WHERE phone='{$phone}' OR phone2='{$phone}' OR phone3='{$phone}'  OR phone4='{$phone}'
            ");
        if (!empty($tutor)) {
            $tutor[0]->type = 'tutor';
            return $tutor[0];
        }
    }
}
