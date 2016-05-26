<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpamStateToRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requests', function($table) {
            $table->enum('state', ['new', 'awaiting', 'finished', 'deny', 'spam'])->default('new');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requests', function($table) {
            $table->enum('state', ['new', 'awaiting', 'finished', 'deny'])->default('new');
        });
    }
}
