<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;

class ClientPhonesTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:phones';

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
        $numbers = $this->getNumbers();
        $this->info(count($numbers));
    }

    public function getNumbers()
    {
        $clients = Client::all();

        $client_ids = [];

        foreach ($clients as $client) {
            preg_match("/([\d]{7})/imu", $client->address, $numbers);
            if (count($numbers)) {
                $client_ids[] = $client->id;
            }
        }

        return $client_ids;
    }
}
