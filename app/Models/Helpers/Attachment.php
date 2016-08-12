<?php

namespace App\Models\Helpers;

class Attachment
{
    /**
     * Ошибки
     */
    public static function errors($attachment)
    {
        $attachment_date = new \DateTime($attachment->getClean('date'));
        // очень важно получать архив именно так, иначе будет рассинхрон при сохранении/обновлении ошибок
        // "При первом сохранении изменений ошибка не пропадает. При повторном сохранении ошибка пропадает"
        $archive = \App\Models\Archive::where('attachment_id', $attachment->id)->first();

        $errors = [];

        if (! $attachment->grade) {
            $errors[] = 1;
        }

        if (! count($attachment->subjects)) {
            $errors[] = 2;
        }

        if (empty(trim($attachment->comment))) {
            $errors[] = 3;
        }

        if ($attachment->forecast && $attachment->forecast < 90) {
            $errors[] = 16;
        } else
        if ($attachment->forecast > 3000) {
            $errors[] = 17;
        }

        if ($archive) {
            $archive_date = new \DateTime($archive->getClean('date'));
            $last_lesson_date = \App\Models\Attachment::getLastLessonDate($attachment->id);
            $last_meeting_date = $attachment->last_meeting_date;

            if ($archive->getClean('date') <= $attachment->getClean('date')) {
                $errors[] = 4;
            }
            if (empty(trim($archive->comment))) {
                $errors[] = 5;
            }

            $x = $archive->total_lessons_missing + $attachment->account_data_count;

            if (! $attachment->forecast && $x) {
                $errors[] = 6;
            }

            if (! $x && $attachment->forecast) {
                $errors[] = 7;
            }

            if (! $archive->total_lessons_missing && $last_lesson_date && $archive->getClean('date') != $last_lesson_date) {
                $errors[] = 9;
            }

            if (! $archive->total_lessons_missing) {
                if ($attachment->hide) {
                    if (! $attachment->account_data_count && $archive_date->diff($attachment_date)->format("%a") != 7) {
                        $errors[] = 13;
                    }
                } else {
                    if ($archive->getClean('date') == $last_lesson_date && $attachment->accounts()->exists() && $last_meeting_date && $archive->getClean('date') <= $last_meeting_date) {
                        $errors[] = 10;
                    }
                    if (! $attachment->account_data_count && $archive_date->diff($attachment_date)->format("%a") == 7) {
                        $errors[] = 11;
                    }
                }
            }

            if ($attachment->hide) {
                if ($archive->total_lessons_missing) {
                    $errors[] = 8;
                }
                if ($last_lesson_date && $archive->getClean('date') != $last_lesson_date) {
                    $errors[] = 12;
                }

                if ($last_meeting_date && $archive->getClean('date') > $last_meeting_date && ($x || $attachment->forecast)) {
                    $errors[] = 15;
                }
            }
        } else {
            if ($attachment->hide) {
                $errors[] = 14;
            }
        }

        sort($errors);
        return implode(',', $errors);
    }
}
