<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeToTime1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        dbEgecrm('time')->insert([
            ['day' => 1, 'time' => '17:30'],
            ['day' => 2, 'time' => '17:30'],
            ['day' => 3, 'time' => '17:30'],
            ['day' => 4, 'time' => '17:30'],
            ['day' => 5, 'time' => '17:30']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time', function (Blueprint $table) {
            //
        });
    }
}
