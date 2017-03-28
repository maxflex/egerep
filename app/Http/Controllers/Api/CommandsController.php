<?php

namespace App\Http\Controllers\Api;

use App\Jobs\ModelErrors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Service\Settings;
use Illuminate\Support\Facades\Queue;
use App\Jobs\UpdateDebtsTable;

class CommandsController extends Controller
{
    /**
     * Пересчитать по всем преподам
     */
    public function postRecalcDebt(Request $request)
    {
        $attachments_count = \DB::table('attachments')->where('forecast', '>', 0)->count();
        $steps_count = ceil($attachments_count / UpdateDebtsTable::STEP) - 1;
        foreach(range(0, $steps_count) as $step) {
            dispatch(new UpdateDebtsTable([
                'step'         => $step,
                'is_last_step' => $step == $steps_count,
            ]));
        }
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
