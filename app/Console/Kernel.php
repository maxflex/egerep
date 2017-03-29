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
        Commands\CalcForecast::class,
        Commands\CalcMinutes::class,
        Commands\CalcModelErrors::class,
        Commands\CalcSummary::class,
        Commands\CalcTutorMargin::class,
        Commands\CleanEntityPhones::class,
        Commands\SyncMango::class,
        Commands\RecalcGraph::class,
        Commands\RecalcTutorData::class,
        Commands\RecalcTutorDistances::class,
        Commands\UpdateClientGrade::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->call(function() {
        //     $attachments_count = \DB::table('attachments')->where('forecast', '>', 0)->count();
        //     $steps_count = ceil($attachments_count / UpdateDebtsTable::STEP) - 1;
        //     foreach(range(0, $steps_count) as $step) {
        //         dispatch(new UpdateDebtsTable([
        //             'step'         => $step,
        //             'is_last_step' => $step == $steps_count,
        //         ]));
        //     }
        // })->dailyAt('02:30'); // это выполняется примерно полчаса
        $schedule->command('calc:summary')->dailyAt('03:15'); // затем должно запуститься это
        $schedule->command('sync:mango')->everyMinute();

        // вспомогательные таблицы для ege-repetitor.ru
        $schedule->command('recalc:tutor_distances')->dailyAt('03:00');
        $schedule->command('recalc:tutor_data')->dailyAt('03:30');
    }
}
