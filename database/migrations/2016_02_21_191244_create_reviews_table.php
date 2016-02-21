<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attachment_id')->unsigned();
            $table->foreign('attachment_id')->references('id')->on('attachments')->onDelete('cascade');
            $table->integer('score')->nullable();
            $table->string('signature');
            $table->text('comment');
            $table->integer('user_id')->unsigned();
            $table->enum('state', ['unpublished', 'published']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('reviews');
    }
}
