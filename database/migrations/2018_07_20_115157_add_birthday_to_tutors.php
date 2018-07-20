<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBirthdayToTutors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->date('birthday');
        });
        \DB::table('tutors')->where('birth_year', '>', 0)->update([
            'birthday' => \DB::raw("CONCAT(birth_year, '-01-01')")
        ]);
        Schema::table('tutors', function (Blueprint $table) {
            $table->dropColumn('birth_year');
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
