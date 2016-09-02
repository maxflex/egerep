<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vocations', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('work_off')->default(false);
            $table->string('comment', 1000);
            $table->string('approved_by');
            $table->text('data');
            $table->integer('user_id')->index();
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
        Schema::drop('vocations');
    }
}
