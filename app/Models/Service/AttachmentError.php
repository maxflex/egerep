<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;
use App\Models\Attachment;

class AttachmentError extends Model
{
    protected static $commaSeparated = ['codes'];

    /**
     * Ошибки перед сохранением
     */
    public static function get($attachment)
    {
        $attachment_date = new \DateTime($attachment->getClean('date'));
        $archive = $attachment->archive;
        $review = $attachment->review;

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

        if ($attachment->hide && $attachment->total_lessons_missing) {
            $errors[] = 16;
        }

        if ($archive) {
            $archive_date = new \DateTime($archive->getClean('date'));
            $last_lesson_date = Attachment::getLastLessonDate($attachment->id);

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

            if ($archive->total_lessons_missing && $attachment->hide) {
                $errors[] = 8;
            }

            if (! $archive->total_lessons_missing && $archive->getClean('date') != $last_lesson_date) {
                $errors[] = 12;
            }

            if (! $archive->total_lessons_missing) {
                if ($attachment->hide) {
                    if (($archive->getClean('date') != $last_lesson_date) || (! $attachment->account_data_count && $archive_date->diff($attachment_date)->format("%a") != 7)) {
                        $errors[] = 14;
                    }
                } else {
                    if (($attachment->accounts()->exists() && $archive->getClean('date') <= $attachment->last_account_date && $archive->getClean('date') == $last_lesson_date) || (! $attachment->account_data_count && $archive_date->diff($attachment_date)->format("%a") != 7)) {
                        $errors[] = 13;
                    }
                }
            }
            if ($attachment->hide && $archive->getClean('date') > $attachment->last_account_date) {
                $errors[] = 17;
            }
        } else {
            if ($attachment->hide) {
                $errors[] = 15;
            }
        }

        if ($review) {
            if ($review->state == 'published') {
                if (empty(trim($review->comment))) {
                    $errors[] = 9;
                }
                if (empty(trim($review->signature))) {
                    $errors[] = 10;
                }
            }
            if (! $review->score) {
                $errors[] = 11;
            }
        }

        sort($errors);
        return $errors;
    }
}
