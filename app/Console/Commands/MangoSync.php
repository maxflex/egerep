<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Api\Mango;
use App\Models\Service\Settings;

class MangoSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mango:sync';

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
        $t = microtime(true);
        Mango::sync();
        $this->line('Mango sync time' . (microtime(true) - $t) . 's');
    }
}
