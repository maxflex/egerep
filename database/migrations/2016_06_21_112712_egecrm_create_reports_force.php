<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmCreateReportsForce extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->create('reports_force', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_subject')->unsigned();
            $table->integer('id_teacher')->unsigned();
            $table->integer('id_student')->unsigned();
            $table->integer('year')->unsigned();
            $table->unique(['id_subject', 'id_teacher', 'id_student', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('egecrm')->drop('reports_force');
    }
}
