<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\GraphRecalc::class,
        Commands\CalculateMinutes::class,
        Commands\TestTutorQueryTime::class,
        Commands\TutorRetina::class,
        Commands\Transfer::class,
        Commands\TransferTruncate::class,
        Commands\TransferDebt::class,
        Commands\AttachmentClientId::class,
        Commands\RecalcDebt::class,
        Commands\TransferAttachmentCreatedAt::class,
        Commands\ChangeZeroTime::class,
        Commands\TransferAttachmentFromList::class,
        Commands\UpdateClientGrade::class,
        Commands\ForecastCalc::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    }
}
