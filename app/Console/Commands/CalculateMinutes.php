<?php

namespace App\Console\Commands;

use App\Models\Metro;
use Illuminate\Console\Command;

class CalculateMinutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metro:recalc_minutes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'recalculates minutes';

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

        /* @var $metro Metro */
        foreach (Metro::all() as $metro) {
            $metro->minutes = Metro::metersToMinutes($metro->meters);
            $metro->save();
        }

        $this->info('Minutes updated');
    }
}
