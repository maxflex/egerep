<?php

namespace App\Console;

use DB;
use App\Jobs\UpdateDebtsTable;
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
        Commands\MangoSync::class,
        Commands\EgecrmTransferCabinet::class,
        Commands\EgecrmContracts::class,
        Commands\CleanEntityPhones::class,
        Commands\ResetMarkers::class,
        Commands\TransferTutorDeparture::class,
        Commands\SvgAddStations::class,
        Commands\TutorDistancesRecalc::class,
        Commands\RecalcTutorData::class,
        Commands\UpdateDebtsTable::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function() {
            $attachments_count = \DB::table('attachments')->where('forecast', '>', 0)->count();
            $steps_count = ceil($attachments_count / UpdateDebtsTable::STEP) - 1;
            foreach(range(0, $steps_count) as $step) {
                dispatch(new UpdateDebtsTable($step, $step == $steps_count));
            }
        })->dailyAt('02:30'); // это выполняется примерно полчаса
        $schedule->command('summary:calc')->dailyAt('03:15'); // затем должно запуститься это
        $schedule->command('mango:sync')->everyMinute();

        // вспомогательные таблицы для ege-repetitor.ru
        $schedule->command('tutor_distances:recalc')->dailyAt('03:00');
        $schedule->command('recalc:tutor_data')->dailyAt('03:30');
    }
}
