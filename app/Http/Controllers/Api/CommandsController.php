<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Service\Settings;

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
     * Обновление таблицы attachment_errors
     */
    public function postRecalcAttachmentErrors(Request $request)
    {
        Artisan::call('calc:attachment_errors');
        return Settings::get('attachment_errors_updated');
    }
}
