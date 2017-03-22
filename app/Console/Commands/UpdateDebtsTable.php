<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Attachment;
use App\Models\Service\Settings;

class UpdateDebtsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debts:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update debts table';

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
        $t = microtime(true);
        DB::table('debts')->truncate();

        $this->line('Getting attachments...');
        $attachments = DB::table('attachments')->where('forecast', '>', 0)->get();

        $bar = $this->output->createProgressBar(count($attachments));
        $now = now(true);

        foreach($attachments as $attachment) {
            $query = DB::table('archives')->where('attachment_id', $attachment->id);
            // check if attachment exists
            if ($query->exists()) {
                $date_end = $query->value('date');
                $archive_date = $date_end;
            } else {
                $date_end = $now;
                $archive_date = null;
            }

            $date = $attachment->date;

            while ($date <= $date_end) {
                DB::table('debts')->insert([
                    'date'      => $date,
                    'debt'      => $attachment->forecast / 7 * static::pissimisticCoef($date, $archive_date),
                    'tutor_id'  => $attachment->tutor_id,
                    'client_id' => $attachment->client_id,
                ]);
                $date = (new \DateTime($date))->modify('+1 day')->format('Y-m-d');
            }

            $bar->advance();
        }
        $bar->finish();
        $this->info("\nFinished in " . round(microtime(true) - $t, 2) . "s");
        Settings::set('debt_table_updated', now());
    }

    /**
     * Писсимизирующий коэффициент
     */
    public static function pissimisticCoef($date, $archive_date)
    {
        // заархивирован этим летом?
        if ($archive_date !== null) {
            $archive_year = date('Y', strtotime($archive_date));
            $date_year = date('Y', strtotime($date));
            $archive_month_day = date('m-d', strtotime($archive_date));
            $summer_archive = (($archive_year == $date_year) && ($archive_month_day >= '06-01' && $archive_month_day <= '08-31'));
        } else {
            $summer_archive = false;
        }

        $date = date('m-d', strtotime($date));

        // первые 7 дней ноября
        if ($date >= '11-01' && $date <= '11-07') {
            return .6;
        }
        // последние 7 дней декабря 0,54
        if ($date >= '12-25' && $date <= '12-31') {
            return .54;
        }
        // первые 10 дней января - 0,12
        if ($date >= '01-01' && $date <= '01-10') {
            return .12;
        }
        // первые 14 дней мая - 0,54
        if ($date >= '05-01' && $date <= '05-14') {
            return .54;
        }
        // с 1 июня по 31 августа - 0,02 (или 0,72 если была архивации в летний период)
        if ($date >= '06-01' && $date <= '08-31') {
            return $summer_archive ? .72 : .02;
        }
        // остальные дни - 0,72
        return .72;
    }
}
