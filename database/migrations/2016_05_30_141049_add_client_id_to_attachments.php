<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientIdToAttachments extends Migration
{
    /**
     * Run the migrations.
     * после этого запустить php artisan once:attachment_client_id и AddClientIdToAttachments2
     * @return void
     */
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->integer('client_id')->unsigned();
        });

        Artisan::call('once:attachment_client_id');

        Schema::table('attachments', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
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
            $table->dropColumn('client_id');
        });
    }
}
