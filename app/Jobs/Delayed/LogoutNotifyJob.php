<?php

use App\Events\LogoutNotify;

/**
 * Уведомление о логауте
 */

class LogoutNotifyJob extends Job
{
    public function handle($params)
    {
        event(new LogoutNotify($params->user_id));
    }
}