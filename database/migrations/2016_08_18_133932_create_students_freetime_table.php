<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsFreetimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->create('students_freetime', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_student');
            $table->index('id_student');
            $table->integer('day');
            $table->integer('time_id');
            $table->unique(['id_student', 'day', 'time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('students_freetime');
    }
}
