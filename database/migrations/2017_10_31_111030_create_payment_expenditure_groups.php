<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentExpenditureGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_expenditure_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('position')->unsigned();
            $table->timestamps();
        });
        Schema::table('payment_expenditures', function (Blueprint $table) {
            $table->integer('group_id')->unsigned()->nullable();
            $table->foreign('group_id')->references('id')->on('payment_expenditure_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payment_expenditure_groups');
    }
}
