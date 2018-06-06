<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDatesInSmsRating extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_rating', function (Blueprint $table) {
            $table->datetime('call_date')->change();
            $table->datetime('rating_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms_rating', function (Blueprint $table) {
            //
        });
    }
}
