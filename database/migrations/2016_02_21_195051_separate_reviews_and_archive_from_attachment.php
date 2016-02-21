<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeparateReviewsAndArchiveFromAttachment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn([
                'total_lessons_missing',
                'archive_comment',
                'archive_user_id',
                'review_user_id',
                'signature',
                'review_comment',
                'score',
                'archive_on',
                'review_on',
                'archive_date',
                'review_date',
                'archive_date_saved',
                'review_date_saved',
                'archive_status',
                'review_status'
            ]);
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
