<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmCreateDelayedJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->create('delayed_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('class');
            $table->string('params', 1000);
            $table->datetime('run_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('delayed_jobs');
    }
}
