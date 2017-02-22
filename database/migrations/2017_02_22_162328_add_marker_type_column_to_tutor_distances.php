<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMarkerTypeColumnToTutorDistances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tutor_distances', function (Blueprint $table) {
            $table->dropColumn('metro_minutes');
            $table->enum('marker_type', ['green', 'red']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tutor_distances', function (Blueprint $table) {
            $table->dropColumn('marker_type');
        });
    }
}
