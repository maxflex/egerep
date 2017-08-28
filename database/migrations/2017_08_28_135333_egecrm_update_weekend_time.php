<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmUpdateWeekendTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        dbEgecrm('time')->whereId(21)->update(['time' => '10:30']);
        dbEgecrm('time')->whereId(22)->update(['time' => '13:00']);
        dbEgecrm('time')->whereId(23)->update(['time' => '15:30']);
        dbEgecrm('time')->whereId(24)->update(['time' => '18:00']);

        dbEgecrm('time')->whereId(25)->update(['time' => '10:30']);
        dbEgecrm('time')->whereId(26)->update(['time' => '13:00']);
        dbEgecrm('time')->whereId(27)->update(['time' => '15:30']);
        dbEgecrm('time')->whereId(28)->update(['time' => '18:00']);

        dbEgecrm('time')->whereIn('id', [3,7,11,15,19])->update(['time' => '16:30']);
        dbEgecrm('time')->whereIn('id', [29,30,31,32,33])->update(['time' => '17:20']);
        dbEgecrm('time')->whereIn('id', [4,8,12,16,20])->update(['time' => '18:50']);
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
