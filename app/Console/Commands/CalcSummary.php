<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\Tutor;
use Illuminate\Console\Command;
use DB;

class CalcSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summary:calc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'calculates forecast and debt summary table fields';

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
        $this->line('Starting...');

        $date = date('Y-m-d', strtotime('yesterday'));
        $forecast = Attachment::doesntHave('archive')->where('date', $date)->sum('forecast');
        $debt = Tutor::sum('debt_calc');

        DB::table('summaries')->insert([
            ['date' => $date, 'forecast' => $forecast, 'debt' => $debt],
        ]);

        $this->info('Summary calculated');
    }
}
