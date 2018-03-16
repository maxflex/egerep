<?php

namespace App\Jobs\Delayed;

/**
 * Отложенные задачи
 */

abstract class Job
{
    abstract public function handle($params);
}