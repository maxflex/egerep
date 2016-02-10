<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToTeachers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('teachers', function (Blueprint $table) {
            // $table->enum('gender', ['male', 'female']);
            // $table->integer('birth_year')->nullable();
            // $table->integer('start_career_year')->nullable();
            // $table->integer('tb')->nullable();
            // $table->integer('lk')->nullable();
            // $table->integer('js')->nullable();
            // $table->boolean('approved')->default(false);
            //
            // $table->text('contacts');
            // $table->text('price');
            // $table->text('education');
            // $table->text('achievements');
            // $table->text('preferences');
            // $table->text('experience');
            // $table->text('current_work');
            // $table->text('tutoring_experience');
            // $table->text('students_category');
            // $table->text('impression');
            // $table->text('schedule');
            // $table->text('public_desc');
            // $table->text('public_price');
            //
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            //
        });
    }
}
