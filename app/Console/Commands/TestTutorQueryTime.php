<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tutor;

class TestTutorQueryTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:tutor_query_time {--markers} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test tutor query time with and without markers';

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
        ini_set('memory_limit', -1);

        if ($this->option('markers')) {
            $this->line('Getting tutors only with markers...');
            $t1 = microtime(true);
            Tutor::has('markers')->get();
            $this->info('Query time: ' . (microtime(true) - $t1) . 's');
        }

        if ($this->option('all')) {
            $this->line('Getting all tutors...');
            $t1 = microtime(true);
            Tutor::get();
            $this->info('Query time: ' . (microtime(true) - $t1) . 's');
        }
    }
}
