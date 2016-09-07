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
        Commands\TransferAttachmentCreatedAt::class,
        Commands\ChangeZeroTime::class,
        Commands\TransferAttachmentFromList::class,
        Commands\UpdateClientGrade::class,
        Commands\ForecastCalc::class,
        Commands\ClientPhonesTransfer::class,
        Commands\InitDuplicatesTable::class,
        Commands\TransferSummary::class,
        Commands\CalcSummary::class,
        Commands\CheckFinishedRequests::class,
        Commands\TransferActiveClients::class,
        Commands\UpdateRequestUserId::class,
        Commands\ReviewsCreate::class,
        Commands\SetChecked::class,
        Commands\Tutors::class,
        Commands\ModelErrors::class,
        Commands\CalcTutorMargin::class,
        Commands\SendSMSToOldClients::class,
        Commands\CallTwoDays::class,
        Commands\DeleteNotifications::class,
        Commands\UpdateArchivesChecked::class,
        Commands\FillMangoTable::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('summary:calc')->dailyAt('02:30');
    }
}
