<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmCreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->create('tests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_user')->unsigned();
            $table->datetime('created_at');
        });
        Schema::connection('egecrm')->create('test_problems', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_test')->unsigned();
            $table->foreign('id_test')->references('id')->on('tests')->onDelete('cascade');
            $table->mediumText('problem');
            $table->mediumText('answers');
            $table->integer('correct_answer')->nullable();
            $table->integer('score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('egecrm')->drop('tests');
        Schema::connection('egecrm')->drop('test_problems');
    }
}
