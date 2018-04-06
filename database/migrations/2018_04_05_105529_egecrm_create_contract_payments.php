<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmCreateContractPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::connection('egecrm')->create('contract_payments', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->integer('id_contract')->unsigned()->index();
        //     $table->date('date')->nullable();
        //     $table->integer('sum');
        // });

        // Договоры
        dbEgecrm('contract_payments')->truncate();

        $contracts = dbEgecrm('contracts')->get();

        foreach($contracts as $contract) {
            if ($contract->payments_split) {
                $contract_info = dbEgecrm('contract_info')->where('id_contract', $contract->id_contract)->first();
                $sum = intval($contract->discount > 0 ? ($contract->sum * ((100 - $contract->discount) / 100)) : $contract->sum);
                $data = ['id_contract' => $contract->id];
                switch($contract->payments_split) {
                    case 1: {
                        $data['sum'] = $sum;
                        dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => null]));
                    }
                    case 2: {
                        $data['sum'] = round($sum / 2);
                        dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => null]));
                        dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => ($contract_info->year + 1) . '-01-27']));
                        break;
                    }
                    case 3: {
                        $data['sum'] = round($sum / 3);
                        dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => null]));
                        if ($contract->payments_queue) {
                            dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => $contract_info->year . '-11-27']));
                            dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => ($contract_info->year + 1) . '-02-27']));
                        } else {
                            dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => $contract_info->year . '-11-20']));
                            dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => ($contract_info->year + 1) . '-02-20']));
                        }
                        break;
                    }
                    case 8: {
                        $data['sum'] = round($sum / 8);
                        dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => null]));
                        dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => $contract_info->year . '-10-15']));
                        dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => $contract_info->year . '-11-15']));
                        dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => $contract_info->year . '-12-15']));
                        dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => ($contract_info->year + 1) . '-01-15']));
                        dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => ($contract_info->year + 1) . '-02-15']));
                        dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => ($contract_info->year + 1) . '-03-15']));
                        dbEgecrm('contract_payments')->insert(array_merge($data, ['date' => ($contract_info->year + 1) . '-04-15']));
                        break;
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::drop('contract_payments');
    }
}
