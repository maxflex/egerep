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
            Account::recalcDebt($account->date_start, $account->date_end, $account->tutor_id, $account->id);
        }

        // Преподаватели с клиентами, но без встреч
        $no_accounts = DB::table('tutors')
            ->join('attachments', 'attachments.tutor_id', '=', 'tutors.id')
            ->leftJoin('accounts', 'accounts.tutor_id', '=', 'tutors.id')
            ->whereNull('accounts.id')
            ->orderBy('attachments.date', 'asc')
            ->select('attachments.tutor_id', 'attachments.date')
            ->groupBy('attachments.tutor_id')
            ->get();

        foreach ($no_accounts as $account) {
            Account::recalcDebt($account->date, now(true), $account->tutor_id);
        }

        Settings::set('debt_updated', now());

        return [
            'total_debt'    => Tutor::totalDebt(),
            'debt_updated'  => now(),
        ];
    }
}
