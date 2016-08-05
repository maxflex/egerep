<?php

namespace App\Http\Controllers\Api;

use App\Jobs\RecalcAttachmentErrors;
use App\Jobs\RecalcReviewErrors;
use App\Jobs\RecalcTutorErrors;
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
     * Обновление таблицы attachment_errors
     */
    public function postRecalcAttachmentErrors()
    {
<<<<<<< HEAD
        Queue::push(new RecalcAttachmentErrors());
=======
         Queue::push(new RecalcAttachmentErrors());
>>>>>>> bec9b39dc11d0eaa0851949bf2d6687bfbcd7496
    }

    public function postRecalcReviewErrors()
    {
         Queue::push(new RecalcReviewErrors());
    }

    public function postRecalcTutorErrors()
    {
         Queue::push(new RecalcTutorErrors());
    }
}
