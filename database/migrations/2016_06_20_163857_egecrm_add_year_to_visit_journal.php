<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAddYearToVisitJournal extends Migration
{
    /**
     * Add year
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('visit_journal', function (Blueprint $table) {
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
        Schema::table('visit_journal', function (Blueprint $table) {
            //
        });
    }
}
