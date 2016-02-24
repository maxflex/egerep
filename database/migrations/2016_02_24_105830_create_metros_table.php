<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metros', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('marker_id')->unsigned();
            $table->foreign('marker_id')->references('id')->on('markers')->onDelete('cascade');
            $table->float('minutes');
            $table->integer('meters');
            $table->integer('station_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('metros');
    }
}
