<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use DB;

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
	    $this->line('Getting clients..');
		$this->getNumbersFour();
    }

	public function cleanNumbers()
	{
		$clients = Client::all();

		$bar = $this->output->createProgressBar(count($clients));

        foreach ($clients as $client) {
			$client->save();
            $bar->advance();
        }
		$bar->finish();
	}

    public function getNumbersFirst()
    {
        // $clients = DB::table('clients')->get();
		$clients = Client::all();

        // $client_ids = [];
		// $count = 0;

		$bar = $this->output->createProgressBar(count($clients));

        foreach ($clients as $client) {
            preg_match_all("/(\b[\d]{7}\b)/imu", $client->address, $phones);
			if (count($phones[0])) {
				foreach($phones[0] as $phone) {
					if (count($client->phones) < 4) {
						$client->address = str_replace($phone, '', $client->address);
						$client->addPhone('7095' . $phone);
					}
				}
				$client->save();
				// $client->address = str_replace($phones[0],)
				// $count += count($phones[0]);
            }
            $bar->advance();
        }
		$bar->finish();
    }

	public function getNumbersSecond()
    {
		$clients = Client::all();

		$replaced = 0;

		$bar = $this->output->createProgressBar(count($clients));

        foreach ($clients as $client) {
            preg_match_all("/(\b[\+]?[78]?[49][\d]{9}\b)/imu", $client->address, $phones);
			if (count($phones[0])) {
				foreach($phones[0] as $phone) {
					if (count($client->phones) < 4) {
						$client->address = str_replace($phone, '', $client->address);
						$client->addPhone('7' . substr($phone, -10));
						$replaced++;
					}
				}
				$client->save();
				// $client->address = str_replace($phones[0],)
				// $count += count($phones[0]);
            }
            $bar->advance();
        }
		$bar->finish();
		$this->info('Numbers replaced: ' . $replaced);
    }

    public function getNumbersThird()
    {
		$clients = DB::table('clients')->get();

		$replaced = 0;

		// $bar = $this->output->createProgressBar(count($clients));

        foreach ($clients as $client) {
            preg_match_all("/([\d]{3,})/imu", $client->address, $phones);
			if (count($phones[0])) {
				$this->line($client->id);
				foreach($phones[0] as $phone) {
					$replaced++;
				}
				// $client->address = str_replace($phones[0],)
				// $count += count($phones[0]);
            }
            // $bar->advance();
        }
		// $bar->finish();
		$this->info('Numbers replaced: ' . $replaced);
    }


	public function getNumbersFour()
    {
		$clients = DB::table('clients')->get();
    }

}
