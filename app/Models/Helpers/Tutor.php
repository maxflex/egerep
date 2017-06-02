<?php

namespace App\Models\Helpers;

class Tutor
{
    // поля, использующиеся на внешних сайтах
    const PUBLIC_FIELDS = [
        'first_name',
        'middle_name',
        'subjects',
        'public_desc',
        'photo_extension',
        'start_career_year',
        'birth_year',
        'lesson_duration',
        'public_price',
        'departure_price',
        'education',
        'achievements',
        'preferences',
        'experience',
        'tutoring_experience',
        'grades',
        'gender',
        'lk',
        'tb',
        'js',
        'video_link',
        'video_duration',
        'description',
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
        if ($tutor->public_desc || in_array(parseInt($tutor->state), [4, 5])) {
            foreach(self::PUBLIC_FIELDS as $field) {
                if (! $tutor->{$field}) {
                    $errors[] = 3;
                    break;
                }
            }
        }

        // статус репетитора: опубликован, к одобрению, одобрено, однако + в выезде указана хотя бы 1 станция + не указана минимальная цена выезда
        if (($tutor->public_desc || in_array(parseInt($tutor->state), [4, 5])) && $tutor->svg_map && ! $tutor->departure_price) {
            $errors[] = 4;
        }

        // статус репетитора опубликован + у репетитора отустствует фото
        if ($tutor->public_desc && ! $tutor->has_photo_cropped) {
            $errors[] = 5;
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
