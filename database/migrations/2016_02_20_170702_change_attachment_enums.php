<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAttachmentEnums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn(['archive_status', 'review_status']);
        });
        Schema::table('attachments', function (Blueprint $table) {
            $table->enum('archive_status', ['impossible', 'possible'])->default('impossible');
            $table->enum('review_status', ['unpublished', 'published'])->default('unpublished');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attachments', function (Blueprint $table) {
            //
        });
    }
}
