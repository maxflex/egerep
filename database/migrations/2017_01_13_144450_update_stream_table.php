<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStreamTable extends Migration
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
            $table->string('action');
            $table->string('type')->nullable();
            $table->integer('step')->unsigned();
            $table->integer('search')->unsigned();
            $table->integer('page')->unsigned()->nullable();
            $table->integer('position')->unsigned()->nullable();
            $table->integer('tutor_id')->unsigned()->nullable();
            $table->foreign('tutor_id')->references('id')->on('tutors')->onDelete('cascade');
            $table->string('google_id');
            $table->string('yandex_id');
            $table->boolean('mobile')->default(false);
            $table->integer('place')->unsigned()->nullable();
            $table->integer('sort')->unsigned()->nullable();
            $table->string('subjects');
            $table->integer('station_id')->unsigned()->nullable();
            $table->integer('depth')->nullable();
            $table->string('referer', 1000);
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
        Schema::drop('stream');
    }
}
