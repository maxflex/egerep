<?php

namespace App\Models\Helpers;

class Tutor
{
    /**
     * Ошибки
     */
    public static function errors($tutor)
    {
        $errors = [];

        if ($tutor->public_desc) {
            if (! $tutor->has_photo_original) {
                $errors[] = 1;
            }

            if (! $tutor->has_photo_cropped) {
                $errors[] = 2;
            }

            if (! $tutor->public_price) {
                $errors[] = 3;
            }

            if ($tutor->isPublished() || $tutor->state == 5) {
                // если нет зеленых меток + выезд невозможен
                if (! collect($tutor->markers)->where('type', 'green')->count() && ! $tutor->svg_map) {
                    $errors[] = 4;
                }
            }
        }

        sort($errors);
        return implode(',', $errors);
    }

    public static function generateLkLink($tutor_id)
    {
        $salt = 'AxQWRu2y3PhE1D';
        $date = date('Y-m-d H');
        $hash = md5($salt . $date);
        $tutor_id_hash = md5($tutor_id . $salt . $hash);
        return "https://ege-repetitor.ru/login/{$hash}{$tutor_id_hash}";
    }
}
