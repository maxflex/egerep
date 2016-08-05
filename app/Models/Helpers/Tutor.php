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
            if (!$tutor->photo_extension) {
                $errors[] = 1;
            }

            if (!$tutor->public_price) {
                $errors[] = 2;
            }
        }

        sort($errors);
        return implode(',', $errors);
    }
}
