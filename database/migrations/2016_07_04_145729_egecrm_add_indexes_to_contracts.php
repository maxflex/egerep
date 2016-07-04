<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAddIndexesToContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('contracts', function (Blueprint $table) {
            $table->index('id_student');
            $table->index('id_contract');
            $table->index('year');
        });
        Schema::connection('egecrm')->table('contract_subjects', function (Blueprint $table) {
            $table->index('status');
            $table->index('id_contract');
        });
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
