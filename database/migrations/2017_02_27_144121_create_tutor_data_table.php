<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTutorDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tutor_data', function (Blueprint $table) {
            $table->integer('tutor_id')->unsigned();
            $table->foreign('tutor_id')->references('id')->on('tutors')->onDelete('cascade');
            $table->integer('clients_count');
            $table->integer('reviews_count');
            $table->text('svg_map')->nullable();
            $table->date('first_attachment_date')->nullable();
            $table->float('review_avg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tutor_data');
    }
}
