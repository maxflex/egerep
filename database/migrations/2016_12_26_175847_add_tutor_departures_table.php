<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTutorDeparturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tutor_departures', function (Blueprint $table) {
            $table->integer('tutor_id')->index();
            $table->integer('station_id');

            $table->unique(['tutor_id', 'station_id']);
        });

        DB::statement('ALTER TABLE tutors CHANGE svg_map _svg_map TEXT NOT NULL');

        Artisan::call('transfer:tutor_departure');

//        Schema::table('tutors', function (Blueprint $table) {
//            $table->dropColumn('_svg_map');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tutor_departures');
        DB::statement('ALTER TABLE tutors CHANGE _svg_map svg_map TEXT NOT NULL');
    }
}
