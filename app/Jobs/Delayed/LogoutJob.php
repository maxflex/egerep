<?php

namespace App\Jobs\Delayed;

/**
 * Отложенный логаут пользователя
 */

class LogoutJob extends Job
{
    public function handle($params)
    {
        session_id($params->session_id);
        session_start();
        session_destroy();
    }
}