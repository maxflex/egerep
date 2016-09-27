<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmFreetimeDropDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

     const TIME = [
         1 => [1, 2, 7, 8],
         2 => [1, 2, 7, 8],
         3 => [1, 2, 7, 8],
         4 => [1, 2, 7, 8],
         5 => [1, 2, 7, 8],
         6 => [3, 4, 5, 6],
         7 => [3, 4, 5, 6],
     ];


    public function up()
    {
        Schema::connection('egecrm')->table('freetime', function (Blueprint $table) {
//            $table->integer('id_time')->unsigned()->index();
        });

        $data = \DB::connection('egecrm')->table('freetime')->get();

        foreach($data as $d) {
//            \DB::connection('egecrm')->table('freetime')->whereId($d->id)->update([
//                'id_time' => static::toTimeid($d->day, $d->time_id)
//            ]);
        }

        Schema::connection('egecrm')->table('freetime', function (Blueprint $table) {
//            $table->dropColumn('day');
//            $table->dropColumn('time_id');
        });
    }

    private static function toTimeid($day, $time)
    {
        $index = array_search($time, self::TIME[$day]);
        return (($day - 1) * 4 + 1) + $index;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('freetime', function (Blueprint $table) {
            //
        });
    }
}
