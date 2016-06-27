<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAddReviewUserIdToStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('students', function (Blueprint $table) {
            $table->integer('id_user_review')->unsigned();
            $table->index('id_user_review');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('egecrm')->table('students', function (Blueprint $table) {
            $table->dropColumn('id_user_review');
        });
    }
}
