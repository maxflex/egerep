<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSourceRemaindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_source_remainders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('source_id')->unsigned();
            $table->foreign('source_id')->references('id')->on('payment_sources')->onDelete('cascade');
            $table->float('remainder')->nullable();
            $table->date('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payment_source_remainders');
    }
}
