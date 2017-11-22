<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmDropOfficialPriceFromVj extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::connection('egecrm')->table('visit_journal', function (Blueprint $table) {
        //     $table->dropColumn('insurance');
        //     $table->dropColumn('teacher_price_official');
        // });
        // Schema::connection('egecrm')->table('groups', function (Blueprint $table) {
        //     $table->dropColumn('teacher_price_official');
        // });
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
