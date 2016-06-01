<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use DB;
use App\Http\Requests;
use App\Models\Tutor;
use App\Models\Account;
use App\Models\Settings;
use App\Http\Controllers\Controller;

class CommandsController extends Controller
{
    /**
     * Пересчитать по всем преподам
     */
    public function getRecalcDebt()
    {
        $accounts = Account::all();

        foreach ($accounts as $account) {
            $account->recalcDebt($account->date_start, $account->date_end);
        }

        // Преподаватели с клиентами, но без встреч
        // Tutor::doesntHave('accounts')
        // $attachments = Attachment::doesntHave('accounts')
        // DB::table('attachments')->leftJoin('accounts', 'accounts.tutor_id', '=', 'attachments.tutor_id')
        //     ->whereNull('accounts.id');

        Settings::set('debt_updated', now());

        return [
            'total_debt'    => Tutor::totalDebt(),
            'debt_updated'  => now(),
        ];
    }
}
