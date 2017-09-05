<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveRatingOldFromTutorData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tutor_data', function (Blueprint $table) {
            $table->dropColumn('review_avg');
        });
        Schema::table('tutor_data', function (Blueprint $table) {
            $table->renameColumn('review_avg_new', 'review_avg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tutor_data', function (Blueprint $table) {
            //
        });
    }
}
