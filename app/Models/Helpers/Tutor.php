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

            // если нет зеленых меток + выезд невозможен
            if (! collect($tutor->markers)->where('type', 'green')->count() && ! $tutor->svg_map) {
                $errors[] = 4;
            }
        }

        sort($errors);
        return implode(',', $errors);
    }
}
