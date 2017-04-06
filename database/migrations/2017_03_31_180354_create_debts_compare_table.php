<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDebtsCompareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debts_compare', function (Blueprint $table) {
            $table->date('date');
            $table->integer('debt_before');
            $table->integer('debt_after');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('debts_compare', function (Blueprint $table) {
            //
        });
    }
}
