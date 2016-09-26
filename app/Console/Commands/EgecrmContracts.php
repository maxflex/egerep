<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class EgecrmContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'egecrm:contracts';

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
        // получить все оригинальные договоры
        $contracts = DB::connection('egecrm')->table('contracts')->whereRaw("(id_contract=0 OR id_contract IS NULL)")->get();

        $bar = $this->output->createProgressBar(count($contracts));

        foreach($contracts as $contract) {
            DB::connection('egecrm')->table('contracts')->whereId($contract->id)->delete();
            $changes = [];

            // присвоить id договора
            $contract->id_contract = $contract->id;

            // пытаемся найти версии договора
            $versions = DB::connection('egecrm')->table('contracts')->where('id_contract', $contract->id)->get();

            // если есть версии
            if (count($versions)) {
                $version_ids = Collect($versions)->pluck('id')->all();
                // удаляем всё из бд, чтобы освободить ID
                DB::connection('egecrm')->table('contracts')->whereIn('id', Collect($versions)->pluck('id')->all())->delete();

                // swap
                $last_version_id = end($versions)->id;

                if (count($versions) > 1) {
                    foreach(range(1, count($versions) - 1) as $i) {
                        $changes[$versions[$i]->id] = $version_ids[$i - 1];
                        $versions[$i]->id = $version_ids[$i - 1];
                        $versions[$i]->id_student = $contract->id_student;
                    }
                }

                $changes[$versions[0]->id] = $contract->id;
                $versions[0]->id = $contract->id;
                $versions[0]->id_student = $contract->id_student;

                $changes[$contract->id] = $last_version_id;
                $contract->id = $last_version_id;

                foreach($versions as $version) {
                    $version = (array)$version;
                    DB::connection('egecrm')->table('contracts')->insert($version);
                }

                // меняем contract_subjects
                if (count($changes)) {
                    $contract_subjects = Collect(DB::connection('egecrm')->table('contract_subjects')->whereIn('id_contract', array_keys($changes))->get());
                    DB::connection('egecrm')->table('contract_subjects')->whereIn('id_contract', array_keys($changes))->delete();
                    foreach($changes as $oldId => $newId) {
                        $cs = $contract_subjects->where('id_contract', $oldId)->all();
                        foreach($cs as $c) {
                            unset($c->id);
                            $c->id_contract = $newId;
                            try {
                                DB::connection('egecrm')->table('contract_subjects')->insert((array)$c);
                            }
                            catch (\Exception $e) {
                                \Log::info('Error: ' . json_encode((array)$c));
                                \Log::info('Message: ' . $e->getMessage());
                            }
                        }
                    }
                    // \Log::info('Changes: ' . json_encode($changes));
                }
            }
            DB::connection('egecrm')->table('contracts')->insert((array)$contract);

            $bar->advance();
        }
        // update contracts set id_student = null, `year` = null, grade = null where id != id_contract
        // DB::connection('egecrm')->table('contracts')->where('id', '<>', 'id_contract')->update([
        //     'grade'      => null,
        //     'year'       => null,
        //     'id_student' => null,
        // ]);
        $bar->finish();
    }
}
