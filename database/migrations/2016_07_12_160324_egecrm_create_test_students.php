<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmCreateTestStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->create('test_students', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_student')->unsigned();
            $table->index('id_student');

            $table->boolean('intermediate')->default(false);

            $table->integer('id_test')->unsigned();
            $table->foreign('id_test')->references('id')->on('tests')->onDelete('cascade');

            $table->integer('score')->default(0);

            $table->datetime('date_start');
            $table->datetime('date_finish');

            $table->unique(['id_student', 'id_test']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('egecrm')->drop('test_students');
    }
}
