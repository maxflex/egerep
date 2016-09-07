<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Api\Mango;
use App\Models\Service\Settings;

class FillMangoTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:mango_table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->line('Starting....');
        // DB::table('mango')->truncate();
        Mango::generateStats();
        Settings::set('mango_sync_time', time());
    }
}
