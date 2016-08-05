<?php

namespace App\Http\Controllers\Api;

use App\Jobs\ModelErrors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Service\Settings;
use Illuminate\Support\Facades\Queue;

class CommandsController extends Controller
{
    /**
     * Пересчитать по всем преподам
     */
    public function postRecalcDebt(Request $request)
    {
        event(new \App\Events\DebtRecalc);

        return [
            'total_debt'    => \App\Models\Tutor::totalDebt(),
            'debt_updated'  => now(),
        ];
    }

    /**
     * Получить информацию по номеру телефона
     */
    public function postMangoStats(Request $request)
    {
        return \App\Models\Api\Mango::getStats($request->number);
    }

    /*
     * Обновление ошибок модели
     */
    public function postModelErrors(Request $request)
    {
         Queue::push(new ModelErrors($request->model));
    }
}
