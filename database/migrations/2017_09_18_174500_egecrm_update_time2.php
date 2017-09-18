<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmUpdateTime2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        dbEgecrm('time')->whereIn('id', [1,5,9,13,17])->update(['time' => '10:30']);
        dbEgecrm('time')->whereIn('id', [2,6,10,14,18])->update(['time' => '13:00']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
