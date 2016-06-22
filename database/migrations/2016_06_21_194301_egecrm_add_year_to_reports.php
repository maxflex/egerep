<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAddYearToReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('reports', function (Blueprint $table) {
            $table->integer('year')->default(2015);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            //
        });
    }
}
