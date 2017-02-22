<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTutorDistancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('tutor_distances');
        Schema::create('tutor_distances', function (Blueprint $table) {
            $table->integer('tutor_id')->unsigned();
            $table->integer('station_id')->unsigned()->index();
            $table->float('marker_distance');
            $table->float('metro_distance');
            $table->unique(['tutor_id', 'station_id']);
            $table->foreign('tutor_id')->references('id')->on('tutors');
        });

        Artisan::call('tutor_distances:recalc');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tutor_distances');
    }
}
