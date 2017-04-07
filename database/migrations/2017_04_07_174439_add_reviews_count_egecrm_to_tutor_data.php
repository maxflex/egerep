<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReviewsCountEgecrmToTutorData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tutor_data', function(Blueprint $table) {
            $table->integer('reviews_count_egecrm')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tutor_data', function(Blueprint $table) {
            $table->dropColumn('reviews_count_egecrm');
        });
    }
}
