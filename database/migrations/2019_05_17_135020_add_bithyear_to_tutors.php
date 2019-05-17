<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBithyearToTutors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->unsignedInteger('birth_year');
        });

        \DB::statement("UPDATE tutors SET birth_year = CAST(YEAR(birthday) AS UNSIGNED)");

        Schema::table('tutors', function (Blueprint $table) {
            $table->dropColumn('birthday');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tutors', function (Blueprint $table) {
            //
        });
    }
}
