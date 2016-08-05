<?php

namespace App\Models\Helpers;

class Review
{
    /**
     * Ошибки
     */
    public static function errors($review)
    {
        $errors = [];

        if ($review->state == 'published') {
            if (empty(trim($review->comment))) {
                $errors[] = 3;
            }
        if (empty(trim($review->signature))) {
            $errors[] = 2;
        }
        }
        if (! $review->score) {
            $errors[] = 1;
        }

        sort($errors);
        return implode(',', $errors);
    }
}
