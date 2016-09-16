<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmCreateTimetable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->create('time', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('day')->unsigned()->index();
            $table->string('time', 5)->index();
        });

        \DB::connection('egecrm')->table('time')->insert([
            ['day' => 1, 'time' => '11:00'],
            ['day' => 1, 'time' => '13:30'],
            ['day' => 1, 'time' => '16:15'],
            ['day' => 1, 'time' => '18:40'],

            ['day' => 2, 'time' => '11:00'],
            ['day' => 2, 'time' => '13:30'],
            ['day' => 2, 'time' => '16:15'],
            ['day' => 2, 'time' => '18:40'],

            ['day' => 3, 'time' => '11:00'],
            ['day' => 3, 'time' => '13:30'],
            ['day' => 3, 'time' => '16:15'],
            ['day' => 3, 'time' => '18:40'],

            ['day' => 4, 'time' => '11:00'],
            ['day' => 4, 'time' => '13:30'],
            ['day' => 4, 'time' => '16:15'],
            ['day' => 4, 'time' => '18:40'],

            ['day' => 5, 'time' => '11:00'],
            ['day' => 5, 'time' => '13:30'],
            ['day' => 5, 'time' => '16:15'],
            ['day' => 5, 'time' => '18:40'],

            ['day' => 6, 'time' => '11:00'],
            ['day' => 6, 'time' => '13:30'],
            ['day' => 6, 'time' => '16:15'],
            ['day' => 6, 'time' => '18:40'],

            ['day' => 7, 'time' => '11:00'],
            ['day' => 7, 'time' => '13:30'],
            ['day' => 7, 'time' => '16:15'],
            ['day' => 7, 'time' => '18:40'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('egecrm')->drop('time');
    }
}
