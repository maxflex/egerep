<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use DateTime;

class CallStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call:stats {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync mango table with MANGO API';

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
        if ($this->option('all')) {
            dbEgecrm('call_stats')->truncate();
            // дата первой записи в таблице mango
            $date_start = '2016-09-08';
        } else {
            $date_start = now(true);
        }

        $date_end = now(true);
        //$date_end = '2016-10-01';

        $bar = $this->output->createProgressBar($this->daysBetweenDates($date_start, $date_end));
        while($date_start <= $date_end) {
            $count = DB::select("SELECT count(*) as `cnt` from
                (select 1
                from mango m
                where DATE(FROM_UNIXTIME(m.`start`)) = '{$date_start}'
                	and m.line_number in ('74956468592', '74954886885', '74954886882')
                	and m.from_extension = 0
                	and m.answer > 0
                	and (m.finish - m.answer) > 15
                	and not exists(
                		select 1 from mango m2
                		where m2.from_number = m.from_number
                			and DATE(FROM_UNIXTIME(m2.start)) between
                				DATE_SUB(DATE(FROM_UNIXTIME(m.`start`)), INTERVAL 7 DAY) AND
                				DATE_SUB(DATE(FROM_UNIXTIME(m.`start`)), INTERVAL 1 DAY)
                	)
                group by from_number) x"
            )[0]->cnt;
            dbEgecrm('call_stats')->insert([
                'date' => $date_start,
                'count' => $count,
            ]);
            $date_start = (new DateTime($date_start))->modify('+1 day')->format('Y-m-d');
            $bar->advance();
        }
        $bar->finish();
    }

    private function daysBetweenDates($start, $end)
    {
        $earlier = new DateTime($start);
        $later = new DateTime($end);
        return $later->diff($earlier)->format("%a") + 1;
    }
}
