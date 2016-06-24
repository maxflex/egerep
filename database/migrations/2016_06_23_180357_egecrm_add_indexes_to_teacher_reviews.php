<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAddIndexesToTeacherReviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('teacher_reviews', function (Blueprint $table) {
            $table->index('id_student');
            $table->index('id_teacher');
            $table->index('id_subject');
            $table->integer('year')->default(2015);
            $table->index('year');

            $table->text('admin_comment_final')->nullable();
            $table->integer('admin_rating_final')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_reviews', function (Blueprint $table) {
            //
        });
    }
}
