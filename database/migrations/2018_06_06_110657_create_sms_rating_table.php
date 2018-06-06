<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_rating', function (Blueprint $table) {
            $table->increments('id');
            $table->date('call_date');
            $table->date('rating_date')->nullable();
            $table->smallInteger('rating')->nullable();
            $table->string('number');
            $table->string('user_id');
            $table->string('mango_entry_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sms_rating');
    }
}
