<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('subject_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->integer('client_id')->unsigned();
            $table->integer('tutor_id')->unsigned();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->date('attachment_date');
            $table->integer('grade');
            $table->string('subjects');
            $table->string('attachment_comment');

            $table->date('archive_date');
            $table->integer('total_lessons_missing');
            $table->string('archive_comment');
            $table->integer('archive_user_id')->unsigned();
            $table->enum('archive_status', ['possible', 'impossible']);

            $table->date('review_date');
            $table->integer('review_user_id')->unsigned();
            $table->integer('score');
            $table->string('signature');
            $table->string('review_comment');
            $table->enum('review_status', ['published', 'unpublished']);

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
        Schema::drop('attachments');
    }
}
