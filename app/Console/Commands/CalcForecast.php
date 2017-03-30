<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class CalcForecast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calc:forecast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalc forecast for attachments';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $attachments = DB::table('attachments')->select(DB::raw('attachments.date as attachment_date, archives.date as archive_date, attachments.client_id, attachments.tutor_id, attachments.id'))
                            ->join('archives', 'archives.attachment_id', '=', 'attachments.id')
                            ->where('archives.total_lessons_missing', null)
                            ->get();

		$bar = $this->output->createProgressBar(count($attachments));

        foreach ($attachments as $attachment) {
            $lessons = DB::table('account_datas')
                            ->where('tutor_id', $attachment->tutor_id)
                            ->where('client_id', $attachment->client_id);

            // если занятия есть
            if ($lessons->exists()) {
	            $query = DB::table('account_datas')
                            ->where('tutor_id', $attachment->tutor_id)
                            ->where('client_id', $attachment->client_id);

                $comm1 = $query->newQuery()->where('commission', 0)->sum('sum');

                $comm2 = $query->newQuery()->where('commission', '>', 0)->sum('commission');

                // сумма комиссий
                $sum = ($comm1 * 0.25) + ($comm2);

                // сумма коэффициентов
                $coef_sum = 0;
                foreach (dateRange($attachment->attachment_date, $attachment->archive_date) as $date) {
                    $coef_sum += \App\Models\Account::pissimisticCoef($date, $attachment->archive_date);
                }

                // прогноз
                $forecast = (7 * $sum) / $coef_sum;

                // округляем до сотых
                if ($forecast > 100) {
                    $forecast = round($forecast, -1);
                }

                // обновляем прогноз
                \App\Models\Attachment::where('id', $attachment->id)->update([
                    'forecast' => $forecast
                ]);
            }
            $bar->advance();
        }
        $bar->finish();
    }
}
