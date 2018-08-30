<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmUpdateTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        dbEgecrm('time')->whereBetween('day', [6, 7])->where('time', '10:30')->update(['time' => '10:20']);

        dbEgecrm('time')->whereBetween('day', [1, 5])->where('time', '13:00')->update(['time' => '12:50']);
        dbEgecrm('time')->whereBetween('day', [6, 7])->where('time', '13:00')->update(['time' => '12:35']);

        dbEgecrm('time')->whereBetween('day', [1, 5])->where('time', '16:30')->update(['time' => '16:20']);
        dbEgecrm('time')->whereBetween('day', [6, 7])->where('time', '15:30')->update(['time' => '14:50']);

        dbEgecrm('time')->whereBetween('day', [6, 7])->where('time', '17:20')->update(['time' => '17:05']);

        dbEgecrm('time')->whereBetween('day', [6, 7])->where('time', '18:00')->update(['time' => '17:05']);

        dbEgecrm('time')->whereBetween('day', [1, 5])->where('time', '18:50')->update(['time' => '18:35']);
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
