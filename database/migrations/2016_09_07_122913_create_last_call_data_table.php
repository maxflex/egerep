<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLastCallDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mango', function (Blueprint $table) {
            $table->increments('id');
            $table->string('recording_id');
            $table->string('start');
            $table->string('finish');
            $table->string('answer')->index();
            $table->integer('from_extension')->index();
            $table->string('from_number')->index();
            $table->integer('to_extension')->index();
            $table->string('to_number')->index();
            $table->integer('disconnect_reason');
        });

        \DB::table('settings')->insert(['key' => 'mango_sync_time']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mango');
    }
}
