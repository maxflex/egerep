<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_method')->default(0);
            $table->integer('debt')->default(0);
            $table->integer('debt_type')->default(0);
            $table->integer('debt_before')->default(0);
            $table->integer('total_commission')->default(0);
            $table->integer('received')->default(0);
            $table->text('comment');
            $table->date('date_end');
            $table->integer('tutor_id')->unsigned();
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
        Schema::drop('accounts');
    }
}
