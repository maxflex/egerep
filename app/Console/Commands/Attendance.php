<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service\Fingerscan;
use DB;

class Attendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance {date_start} {date_end?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill in the attendance';

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
        $date_start = $this->argument('date_start');
        $date_end = $this->argument('date_end') ?: $date_start;

        DB::table('attendance')->whereBetween('date', [$date_start, $date_end])->delete();

        foreach(dateRange($date_start, $date_end) as $current_date) {
            $data = Fingerscan::get($current_date);
            foreach($data as $d) {
                try {
                    DB::table('attendance')->insert([
                        'user_id' => $d->user_id,
                        'date'    => $d->date
                    ]);
                } catch (\Exception $e) {}
            }
        }
    }
}
