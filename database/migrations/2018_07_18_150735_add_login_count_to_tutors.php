<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoginCountToTutors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->integer('login_count')->unsigned()->default(0);
        });
        Schema::connection('egecrm')->table('students', function (Blueprint $table) {
            $table->integer('login_count')->unsigned()->default(0);
        });
        Schema::connection('egecrm')->table('representatives', function (Blueprint $table) {
            $table->integer('login_count')->unsigned()->default(0);
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
