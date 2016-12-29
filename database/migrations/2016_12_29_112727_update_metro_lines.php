<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMetroLines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('stations')->whereId(188)->update([
            'lat' => '55.7576',
            'lng' => '37.6577'
        ]);
        \DB::table('stations')->whereId(63)->update([
            'lng' => '37.6595',
            'line_id' => 5
        ]);
        \DB::table('stations')->whereId(47)->update([
            'line_id' => 5
        ]);
        \DB::table('stations')->whereId(15)->update([
            'line_id' => 5
        ]);
        \DB::table('stations')->whereId(51)->update([
            'line_id' => 5
        ]);
        \DB::table('stations')->whereId(197)->update([
            'line_id' => 11
        ]);
        \DB::table('stations')->whereId(62)->update([
            'line_id' => 4
        ]);
        \DB::table('stations')->whereId(91)->update([
            'line_id' => 5
        ]);
        \DB::table('stations')->whereId(92)->update([
            'line_id' => 5
        ]);
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
