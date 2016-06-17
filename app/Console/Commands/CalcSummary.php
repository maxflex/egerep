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
    protected $description = 'Calculates forecast and debt summary table fields';

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

        $no_archive_attachments = Attachment::doesntHave('archive');

        $date               = date('Y-m-d', strtotime('yesterday'));
        $forecast           = $no_archive_attachments->newQuery()->sum('forecast');
        $debt               = Tutor::sum('debt_calc');
        $new_clients        = Attachment::where('date', $date)->count();
        $active_attachments = $no_archive_attachments->count();

        DB::table('summaries')->insert(compact('date', 'forecast', 'debt', 'new_clients', 'active_attachments'));

        $this->info('Summary calculated');
    }
}
