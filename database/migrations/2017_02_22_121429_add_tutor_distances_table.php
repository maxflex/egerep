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
            $table->foreign('tutor_id')->references('id')->on('tutors');
            $table->integer('station_id')->unsigned()->index();
            $table->float('minutes');
            $table->enum('marker_type', ['green', 'red'])->index();
        });
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
