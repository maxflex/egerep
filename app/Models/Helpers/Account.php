<?php

namespace App\Models\Helpers;

class Account
{
    /**
     * Ошибки
     */
    public static function errors($account)
    {
        $errors = [];

        // в расчете отсутствуют платежи (в том числе взаимозачеты)
        if (! count($account->all_payments)) {
            $errors[] = 1;
        }

        // в расчете не проведено ни одного занятия
        if (! $account->accountData()->exists()) {
            $errors[] = 2;
        }

        sort($errors);
        return implode(',', $errors);
    }
}
