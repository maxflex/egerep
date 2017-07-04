<?php

namespace App\Models\Helpers;

use DB;

class Attachment
{
    /**
     * Ошибки
     */
    public static function errors($attachment)
    {
        $attachment_date = $attachment->getClean('date');
        $attachment_date_obj = new \DateTime($attachment_date);
        // очень важно получать архив именно так, иначе будет рассинхрон при сохранении/обновлении ошибок
        // "При первом сохранении изменений ошибка не пропадает. При повторном сохранении ошибка пропадает"
        $archive = \App\Models\Archive::where('attachment_id', $attachment->id)->first();

        $errors = [];

        // занятий к проводке+проведено занятий в отчетности
        // при этом архивация может быть или не быть
        $x = $attachment->account_data_count;

        // в стыковке не указан класс
        if (! $attachment->grade) {
            $errors[] = 1;
        }

        // в стыковке не указан хотя бы 1 предмет
        if (! count($attachment->subjects)) {
            $errors[] = 2;
        }

        // в стыковке поле условия (это которое над прогнозом) пусто
        if (empty(trim($attachment->comment))) {
            $errors[] = 3;
        }

        // есть занятия строго до стыковки
        if (self::hasLessons($attachment, '<', $attachment_date)) {
            $errors[] = 6;
        }

        // слишком большой или слишком маленький прогноз
        if ($attachment->forecast && ($attachment->forecast < 100 || $attachment->forecast >= 5000)) {
            $errors[] = 18;
        }

        // слишком большая/маленькая стоимость занятия в отчетности
        if (self::lessons($attachment)->whereRaw('(sum >= 19000 OR sum < 500)')->exists()) {
            $errors[] = 20;
        }

        // слишком большая/маленькая комиссия в отчетности
        if (self::lessons($attachment)->where('commission', '>', 0)->whereRaw('(commission >= 2100 OR commission < 100)')->exists()) {
            $errors[] = 21;
        }

        if ($archive) {
            // занятий к проводке+проведено занятий в отчетности
            // при этом архивация может быть или не быть
            $x += $archive->total_lessons_missing;

            $archive_date       = $archive->getClean('date');
            $archive_date_obj   = new \DateTime($archive_date);
            $last_lesson_date   = \App\Models\Attachment::getLastLessonDate($attachment->id);
            $last_meeting_date  = $attachment->last_meeting_date;
            $client_grade       = intval(DB::table('clients')->whereId($attachment->client_id)->value('grade'));

            // стыковка скрыта И занятий к проводке > 0
            if ($attachment->hide && $archive->total_lessons_missing) {
                $errors[] = 4;
            }

            // есть занятия строго после архивации
            if (self::hasLessons($attachment, '>', $archive_date)) {
                $errors[] = 7;
            }

            // есть занятия строго после последнего расчета
            if ($last_meeting_date && self::hasLessons($attachment, '>', $last_meeting_date)) {
                $errors[] = 8;
            }

            // дата архивации строго раньше даты стыковки
            if ($archive_date < $attachment_date) {
                $errors[] = 9;
            }

            // поле детали архивации пусто
            if (empty(trim($archive->comment))) {
                $errors[] = 10;
            }

            // сумма Х = 0 И дата архивации стоит НЕ через 7 дней после даты стыковки
            if (! $x && $archive_date_obj->diff($attachment_date_obj)->format("%a") != 7) {
                $errors[] = 11;
            }

            // сумма Х = 0 И дата архивации стоит через 7 дней после даты стыковки И стыковка показана
            if (! $x && $archive_date_obj->diff($attachment_date_obj)->format("%a") == 7 && ! $attachment->hide) {
                $errors[] = 12;
            }

            // сумма Х = 0 И прогноз > 0 И архивация есть
            if (! $x && $attachment->forecast) {
                $errors[] = 13;
            }

            // занятий к проводке = 0 И дата архивации не совпадает с датой последнего занятия
            if ($last_lesson_date && ! $archive->total_lessons_missing && $archive_date != $last_lesson_date) {
                $errors[] = 15;
            }

            // занятий к проводке = 0 И дата архивации совпадает с датой последнего занятия И стыковка показана
            if ($last_lesson_date && ! $archive->total_lessons_missing && $archive_date == $last_lesson_date && ! $attachment->hide) {
                $errors[] = 16;
            }

            // разархивация возможна И класс клиента НЕ с 1 по 11
            if ($archive->state == 'possible' && ! in_array($client_grade, range(1, 11))) {
                $errors[] = 17;
            }

            // указано слишком большое значение в занятиях к проводке
            if ($archive->total_lessons_missing >= 100) {
                $errors[] = 19;
            }
        } else {
            // стыковка скрыта И архивация отсутствует
            if ($attachment->hide) {
                $errors[] = 5;
            }
        }

        // ОБЯЗАТЕЛЬНО ниже, потому что $x мог измениться с наличием архивации

        // сумма Х > 0 И прогноз = [0 или пусто]
        if (! $attachment->forecast && $x) {
            $errors[] = 14;
        }

        sort($errors);
        return implode(',', $errors);
    }

    /**
     * Есть ли занятия до/после даты
     */
    private static function hasLessons($attachment, $sign, $date)
    {
        return self::lessons($attachment)->where('date', $sign, $date)->exists();
    }

    /**
     * Есть ли занятия до/после даты
     */
    private static function lessons($attachment)
    {
        return DB::table('account_datas')->where('tutor_id', $attachment->tutor_id)->where('client_id', $attachment->client_id);
    }
}
