<?php

namespace App\Console\Commands;

use App\Http\Controllers\UserstatsController;
use Illuminate\Console\Command;

class SaveTutorStateChanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutorstates:transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saves responsible id changes in tutors';

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
        $result = UserstatsController::transfer();
        if($result) {
            $this->error('smth went wrong');
        } else {
            $this->info('data saved');
        }
    }
}
