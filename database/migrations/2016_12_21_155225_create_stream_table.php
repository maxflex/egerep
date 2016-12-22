<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStreamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stream', function (Blueprint $table) {
            $table->increments('id');
            $table->string('source');
            $table->integer('search')->unsigned();
            $table->string('subjects');
            $table->integer('place')->unsigned()->nullable();
            $table->integer('sort')->unsigned();
            $table->integer('station_id')->unsigned()->nullable();
            $table->integer('page')->unsigned()->nullable();
            $table->integer('step')->unsigned();
            $table->integer('position')->unsigned()->nullable();
            $table->string('client_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('stream');
    }
}
