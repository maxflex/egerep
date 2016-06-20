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

    public static function calcData()
    {
        $forecast           = Attachment::newOrActive()->sum('forecast');
        $debt               = Tutor::sum('debt_calc');
        $new_clients        = Attachment::newest()->count();
        $active_attachments = Attachment::active()->count();
        // @todo: в этом случае newQuery() не работает
        // $no_archive_attachments = Attachment::doesntHave('archive');
        // $new_clients        = $no_archive_attachments->newQuery()->whereNullOrZero('forecast')->count();
        // $active_attachments = $no_archive_attachments->count();

        return compact('forecast', 'debt', 'new_clients', 'active_attachments');
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
        $summary = self::calcData() + compact('date');

        DB::table('summaries')->insert($summary);

        $this->info('Summary calculated');
    }
}
