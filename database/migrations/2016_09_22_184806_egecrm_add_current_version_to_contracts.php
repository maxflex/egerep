<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAddCurrentVersionToContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('contracts', function (Blueprint $table) {
            $table->boolean('current_version')->default(false);
        });
        // RUN THIS SQL:
        // update contracts c
        // join (SELECT *, MAX(id) as max_id FROM contracts GROUP BY id_contract) current_contract ON current_contract.max_id = c.id
        // set c.current_version = 1
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            //
        });
    }
}
