<?php

namespace App\Console;

use DB;
use App\Models\Debt;
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
        Commands\UpdateClientGrade::class,
        Commands\InitDuplicatesTable::class,
        Commands\CalcSummary::class,
        Commands\ModelErrors::class,
        Commands\MangoSync::class,
        Commands\CleanEntityPhones::class,
        Commands\ResetMarkers::class,
        Commands\TutorDistancesRecalc::class,
        Commands\RecalcTutorData::class,
        Commands\RecalcEfficency::class,
        Commands\ConnectStations::class,
        Commands\Attendance::class,
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
            $attachments_count = DB::table('attachments')->where('forecast', '>', 0)->count();
            $steps_count = ceil($attachments_count / UpdateDebtsTable::STEP) - 1;
            foreach(range(0, $steps_count) as $step) {
                dispatch(new UpdateDebtsTable([
                    'step'         => $step,
                    'is_last_step' => $step == $steps_count,
                ]));
            }
        })->dailyAt('02:30'); // это выполняется примерно полчаса
        $schedule->command('summary:calc')->dailyAt('03:15'); // затем должно запуститься это

        $schedule->command('attendance ' . now(true))->dailyAt('23:00');

        $schedule->command('mango:sync')->everyMinute();

        // вспомогательные таблицы для ege-repetitor.ru
        $schedule->command('tutor_distances:recalc')->dailyAt('03:00');
        $schedule->command('recalc:tutor_data')->dailyAt('03:30');
        $schedule->command('recalc:efficency')->dailyAt('22:30');
    }
}
