<?php

namespace App\Models\Helpers;

class Tutor
{
    // поля, использующиеся на внешних сайтах
    const PUBLIC_FIELDS = [
        'first_name',
        'middle_name',
        'public_desc',
        'education',
        'achievements',
        'experience',
        'tutoring_experience',
        'preferences',
        'grades'
    ];

    /**
     * Ошибки
     */
    public static function errors($tutor)
    {
        $errors = [];

        // в анкете нет ни одного телефона
        if (! count($tutor->phones)) {
            $errors[] = 1;
        }

        // дублирование телефонного номера в нескольких анкетах
        if ($tutor->phone_duplicate || $tutor->phone2_duplicate || $tutor->phone3_duplicate || $tutor->phone4_duplicate) {
            $errors[] = 2;
        }

        // статус репетитора: опубликован, к одобрению, одобрено, однако не заполнено любое поле, использующиеся на сайте
        if (! empty(trim($tutor->public_desc)) || in_array(intval($tutor->state), [4, 5])) {
            foreach(self::PUBLIC_FIELDS as $field) {
                if (empty(trim($tutor->getClean($field)))) {
                    $errors[] = 3;
                    break;
                }
            }
        }

        // статус репетитора опубликован + у репетитора отустствует фото
        if (! empty(trim($tutor->public_desc)) && ! $tutor->has_photo_cropped) {
            $errors[] = 4;
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
