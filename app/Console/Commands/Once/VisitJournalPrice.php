<?php

namespace App\Console\Commands\Once;

use Illuminate\Console\Command;

class VisitJournalPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:vj_price';

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
        $student_ids = dbEgecrm('visit_journal')
            ->where('type_entity', 'STUDENT')
            ->where('year', 2017)
            ->pluck('id_entity');

        $bar = $this->output->createProgressBar(count($student_ids));

        foreach($student_ids as $student_id) {
            $last_student_contract = $this->getLastContract($student_id);
            if ($last_student_contract) {
                $price = $last_student_contract->grade == 11 ? 1900 : 1700;

    			if ($last_student_contract->discount) {
    				$price = round($price - ($price * ($last_student_contract->discount / 100)));
    			}

                dbEgecrm('visit_journal')
                    ->where('type_entity', 'STUDENT')
                    ->where('year', 2017)
                    ->where('id_entity', $student_id)
                    ->update(compact('price'));
            }

            $bar->advance();
        }

        $bar->finish();
    }

    private function getLastContract($student_id)
    {
        $contracts = \DB::connection('egecrm')->select("
            SELECT ci.grade, c.discount FROM contracts c
            JOIN contract_info ci ON ci.id_contract = c.id_contract
            WHERE ci.id_student={$student_id} AND c.current_version=1 AND ci.year=2017
            ORDER BY id DESC
            LIMIT 1
        ");
        if (count($contracts)) {
            return $contracts[0];
        }
        return null;
    }
}
