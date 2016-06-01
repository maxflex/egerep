<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TransferPhones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:phones';

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
        $this->getNumbers();
    }

    public function getNumbers()
    {
        $clients = Client::all();

        $client_ids = [];

        foreach ($clients as $client) {
            preg_match("/([\d]{7})/imu", $client->address, $numbers);
            if (count($numbers)) {
	            // echo $tutor->contacts . '<br>';
                $client_ids[] = $client->id;
            }
        }

        $this->info(count($client_ids));
    }
}
