<?php

namespace App\Console\Commands;

use App\Models\Metro;
use Illuminate\Console\Command;

class CalcMinutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calc:metro_minutes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculates minutes to closest stations';

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

        foreach (Metro::all() as $metro) {
            $metro->minutes = Metro::metersToMinutes($metro->meters);
            $metro->save();
        }

        $this->info('Minutes recalculated');
    }
}
