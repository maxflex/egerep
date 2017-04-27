<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAddCntProgramToContractSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('contract_subjects', function (Blueprint $table) {
            $table->integer('count_program')->unsigned()->nullable();
        });
        \DB::connection('egecrm')->statement("UPDATE contract_subjects SET count_program=count");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract_subjects', function (Blueprint $table) {
            //
        });
    }
}
