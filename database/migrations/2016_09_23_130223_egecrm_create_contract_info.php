<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmCreateContractInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_info', function (Blueprint $table) {
            $table->integer('id_contract')->unsigned()->primary();
            $table->integer('grade')->unsigned()->index();
            $table->integer('id_student')->unsigned()->index();
            $table->integer('year')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('contract_info');
    }
}
