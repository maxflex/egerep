<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewClientsAndActiveAttToSummaries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summaries', function (Blueprint $table) {
            $table->integer('new_clients')->default(0);
            $table->integer('active_attachments')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('summaries', function (Blueprint $table) {
            //
        });
    }
}
