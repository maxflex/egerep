<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAddAnswersToTestStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('tests', function (Blueprint $table) {
            $table->text('intro');
        });
        Schema::connection('egecrm')->table('test_students', function (Blueprint $table) {
            $table->string('answers', 1000);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_students', function (Blueprint $table) {
            //
        });
    }
}
