<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQniqueToAttachmentsArchivesReviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unique('attachment_id');
        });
        Schema::table('archives', function (Blueprint $table) {
            $table->unique('attachment_id');
        });
        Schema::table('attachments', function (Blueprint $table) {
            $table->unique(['tutor_id','client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
