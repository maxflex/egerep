<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmUpdateGroupTime extends Migration
{
    const TIME = [
        1 => [1, 2, 7, 8],
        2 => [1, 2, 7, 8],
        3 => [1, 2, 7, 8],
        4 => [1, 2, 7, 8],
        5 => [1, 2, 7, 8],
        6 => [3, 4, 5, 6],
        7 => [3, 4, 5, 6],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('group_time', function (Blueprint $table) {
            // $table->integer('id_time')->unsigned()->index();
            $table->integer('id_cabinet')->unsigned()->index();
        });

        // $data = \DB::connection('egecrm')->table('group_time')->get();
        //
        // foreach($data as $d) {
        //     \DB::connection('egecrm')->table('group_time')->whereId($d->id)->update([
        //         'id_time' => static::toTimeid($d->day, $d->time)
        //     ]);
        // }
        //
        // Schema::connection('egecrm')->table('group_time', function (Blueprint $table) {
        //     $table->foreign('id_time')->references('id')->on('time');
        //     $table->unique(['id_group', 'id_time']);
        // });

        // Schema::connection('egecrm')->table('group_time', function (Blueprint $table) {
        //     $table->dropColumn('day');
        //     $table->dropColumn('time');
        //     $table->dropColumn('id');
        // });

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
        // Schema::connection('egecrm')->table('group_time', function (Blueprint $table) {
        //     $table->dropColumn('id_time');
        // });
    }
}
