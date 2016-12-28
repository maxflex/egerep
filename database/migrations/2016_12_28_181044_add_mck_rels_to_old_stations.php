<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMckRelsToOldStations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        self::addRel(97, 212);
        self::addRel(29, 219);
        self::addRel(27, 221);
        self::addRel(20, 222);
        self::addRel(154, 224);
        self::addRel(94, 225);
        self::addRel(160, 227);
        self::addRel(41, 229);
        self::addRel(2, 230);
        self::addRel(65, 231);
        self::addRel(125, 232);
        self::addRel(64, 233);
        self::addRel(87, 235);
    }

    private static function addRel($a, $b)
    {
        \DB::table('stations')->whereId($a)->update(['rel' => $b]);
        \DB::table('stations')->whereId($b)->update(['rel' => $a]);
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
