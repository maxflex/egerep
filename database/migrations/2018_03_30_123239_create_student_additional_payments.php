<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentAdditionalPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->create('student_additional_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sum');
            $table->integer('id_user');
            $table->integer('id_student');
            $table->string('purpose');
            $table->date('date');
            $table->integer('year')->unsigned();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('egecrm')->drop('student_additional_payments');
    }
}
