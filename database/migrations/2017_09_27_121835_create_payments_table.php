<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->float('sum');
            $table->boolean('loan')->default(false);
            $table->string('purpose', 1000);
            $table->date('date');
            $table->integer('user_id')->unsigned();

            $table->integer('addressee_id')->unsigned()->nullable();
            $table->foreign('addressee_id')->references('id')->on('payment_addressees')->onDelete('set null');

            $table->integer('source_id')->unsigned()->nullable();
            $table->foreign('source_id')->references('id')->on('payment_sources')->onDelete('set null');

            $table->integer('expenditure_id')->unsigned()->nullable();
            $table->foreign('expenditure_id')->references('id')->on('payment_expenditures')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payments');
    }
}
