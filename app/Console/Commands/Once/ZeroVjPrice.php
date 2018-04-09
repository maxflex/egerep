<?php

namespace App\Console\Commands\Once;

use Illuminate\Console\Command;

class ZeroVjPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:vj_zero';

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
        $data = \DB::connection('egecrm')->select("select id_entity, year from visit_journal where type_entity='STUDENT' and (price=0 or price is null) group by id_entity, year");

        $bar = $this->output->createProgressBar(count($data));
        foreach($data as $d) {
            $contract = $this->getFirstContractInYear($d);

            if ($contract) {
                $subjects_count = dbEgecrm('contract_subjects')->where('id_contract', $contract->id)->sum('count');
                $price = round($contract->sum / $subjects_count);

                dbEgecrm('visit_journal')
                    ->where('year', $d->year)
                    ->where('type_entity', 'STUDENT')
                    ->where('id_entity', $d->id_entity)
                    ->whereRaw('(price=0 or price is null)')
                    ->update(compact('price'));
            }


            $bar->advance();
        }
        $bar->finish();
    }

    private function getFirstContractInYear($d)
    {
        $contracts = \DB::connection('egecrm')->select("
            SELECT * from contracts c
            join contract_info ci on c.id_contract = ci.id_contract
            where ci.year={$d->year} and ci.id_student={$d->id_entity} and c.id=c.id_contract
        ");

        $min_id = PHP_INT_MAX;
        $result = null;
        foreach($contracts as $contract) {
            if ($contract->id < $min_id) {
                $min_id = $contract->id;
                $result = $contract;
            }
        }

        return $result;
    }
}
