<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmCreateReportsHelperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->create('reports_helper', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_report')->unsigned()->nullable();

            $table->integer('id_student')->unsigned()->nullable();
            $table->integer('id_teacher')->unsigned()->nullable();
            $table->integer('id_subject')->unsigned()->nullable();
            $table->integer('year')->unsigned()->nullable();
            $table->integer('lesson_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('egecrm')->drop('reports_helper');
    }
}
