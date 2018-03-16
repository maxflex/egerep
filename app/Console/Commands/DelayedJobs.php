<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DelayedJob;

class DelayedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:delayed';

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
	    \Log::info("Getting delayed jobs...");
		// получить все текущие задачи
		$current_jobs = DelayedJob::whereRaw("DATE_FORMAT(run_at, '%Y-%m-%d %H:%i')='" . date('Y-m-d H:i') ."'")->get();

		\Log::info(count($current_jobs) . " jobs found");

		foreach($current_jobs as $job) {
			\Log::info("Handling " . $job->class);
			$job_class = new $job->class;
			$job_class->handle($job->params);
			$job->delete();
		}
    }
}
