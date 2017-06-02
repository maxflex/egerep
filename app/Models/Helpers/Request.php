<?php

namespace App\Models\Helpers;

class Request
{
    /**
     * Ошибки
     */
    public static function errors($request)
    {
        $errors = [];

        // в заявке есть стыковка
        $request_has_attachments = false;
        foreach($request->lists as $list) {
            if ($list->attachments()->exists()) {
                $request_has_attachments = true;
                break;
            }
        }

        // статус заявки выполнено + в заявке нет ни одной стыковки
        if ($request->state == 'finished' && ! $request_has_attachments) {
            $errors[] = 1;
        }

        // статус заявки отказ + в заявке есть стыковки
        if ($request->state == 'deny' && $request_has_attachments) {
            $errors[] = 2;
        }

        // статус заявки отказ + ответственный не установлен
        if ($request->state == 'deny' && ! $request->user_id) {
            $errors[] = 3;
        }

        sort($errors);
        return implode(',', $errors);
    }
}
