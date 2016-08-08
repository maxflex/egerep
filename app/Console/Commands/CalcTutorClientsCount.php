<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\Tutor;
use Illuminate\Console\Command;

class CalcTutorClientsCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutor:calc_clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'calculates and updates clients_count column of tutors';

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
        $this->info('Getting tutors...');
        $tutors = Tutor::all();

        $bar = $this->output->createProgressBar(count($tutors));

        foreach ($tutors as $tutor) {
            $tutor->clients_count = Attachment::where('tutor_id', $tutor->id)->count();
            $tutor->save();
            $bar->advance();
        }
        $bar->finish();
    }
}
