<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Client;
use App\Models\Tutor;

class CleanEntityPhones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entity:cleanphones';

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
        $this->line('updating clients..');
        $clients = Client::all();

        $bar = $this->output->createProgressBar(count($clients));

        foreach ($clients as $client) {
            $client->save();
            $bar->advance();
        }
        $bar->finish();

        $this->line("\n\nupdating tutors...");
        $tutors = Tutor::all();
        $bar = $this->output->createProgressBar(count($tutors));

        foreach ($tutors as $tutor) {
            $tutor->save();
            $bar->advance();
        }
        $bar->finish();
    }
}
