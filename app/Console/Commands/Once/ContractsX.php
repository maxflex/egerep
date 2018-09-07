<?php

namespace App\Console\Commands\Once;

use Illuminate\Console\Command;

class ContractsX extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:contracts_x';

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
        $contracts = \DB::connection('egecrm')->select("
            SELECT c.*, ci.year, ci.grade from contracts c
            join contract_info ci on c.id_contract = ci.id_contract
        ");

        $prices = dbEgecrm('settings')->where('name', 'recommended_prices')->first();
        $prices = json_decode($prices->value, JSON_OBJECT_AS_ARRAY);

        $result = [];
		$errors = [];
        $bar = $this->output->createProgressBar(count($contracts));
        foreach($contracts as $contract) {
	        if (isset($prices[$contract->year][$contract->grade])) {
		        $contract_subject_sum = dbEgecrm('contract_subjects')->where('id_contract', $contract->id)->sum('count');
	            $price = $contract_subject_sum * $prices[$contract->year][$contract->grade];
	            if ($contract->discount) {
	                $price = round($price - ($price * ($contract->discount / 100)));
	            }
	            if ($price != $contract->sum) {
	                $result[] = $contract->id_contract;
	            }
	        } else {
		        $errors[] = $contract->id_contract;
		    }
		    $bar->advance();
	    }

		$this->line("\n");

        $result = array_unique($result);
		$this->info(implode(', ', $result));

		$errors = array_unique($errors);
		$this->error(implode(', ', $errors));

        $bar->finish();
    }

}
