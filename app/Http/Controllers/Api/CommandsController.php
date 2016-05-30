<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CommandsController extends Controller
{
    /**
     * Пересчитать по всем преподам
     */
    public function getRecalcDebt()
    {
        $accounts = \App\Models\Account::all();

        foreach ($accounts as $account) {
            $account->recalcDebt();
        }
    }
}
