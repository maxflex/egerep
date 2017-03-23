<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateDebtsTable extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $priority;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($priority)
    {
        $this->priority = $priority;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(2);
        \Log::info("Job {$this->priority} done");
    }
}
