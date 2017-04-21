<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmDropExternalColumnFromContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $external_contract_ids = dbEgecrm('contracts')->where('external', 1)->pluck('id_contract');
        dbEgecrm('contract_info')->whereIn('id_contract', $external_contract_ids)->update(['grade' => 14]);
        Schema::connection('egecrm')->table('contracts', function (Blueprint $table) {
            $table->dropColumn('external');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
