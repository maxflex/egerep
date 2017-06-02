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
            // отзыв опубликован + нет отзыва
            if (empty(trim($review->comment))) {
                $errors[] = 1;
            }
            // отзыв опубликован + нет подписи
            if (empty(trim($review->signature))) {
                $errors[] = 2;
            }
            // отзыв опубликован + оценка НЕ = от 1 до 10
            if (! in_array(parseInt($review->score), range(1, 10))) {
                $errors[] = 3;
            }
        } else {
            // оценка = от 6 до 10 + отзыв не опубликован
            if (in_array(parseInt($review->score), range(6, 10))) {
                $errors[] = 6;
            }
        }

        // оценка с 1 по 10 + текст отзыва пусто
        if (in_array(parseInt($review->score), range(1, 10)) && empty(trim($review->comment))) {
            $errors[] = 4;
        }

        // текст отзыва НЕ пусто + оценка пусто
        if (! empty(trim($review->comment)) && ! $review->score) {
            $errors[] = 5;
        }

        sort($errors);
        return implode(',', $errors);
    }
}
