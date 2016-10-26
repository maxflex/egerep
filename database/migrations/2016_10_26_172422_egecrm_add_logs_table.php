<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAddLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->create('logs', function (Blueprint $table) {
            $table->increments('id');

            $table->string('table');
            $table->index('table');

            $table->integer('row_id');
            $table->index('row_id');

            $table->integer('user_id');
            $table->index('user_id');

            $table->text('data')->nullable();

            $table->string('type', 100);
            $table->index('type');

            $table->datetime('created_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('egecrm')->drop('logs');
    }
}
