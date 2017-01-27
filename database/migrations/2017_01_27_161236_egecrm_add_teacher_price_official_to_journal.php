<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAddTeacherPriceOfficialToJournal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('visit_journal', function (Blueprint $table) {
            $table->integer('teacher_price_official')->nullable();
            $table->decimal('insurance', 10, 2)->nullable();
            $table->decimal('ndfl', 10, 2)->nullable();
        });
        \DB::connection('egecrm')->table('payments')->where('id_status', 7)->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('egecrm')->table('visit_journal', function (Blueprint $table) {
            $table->dropColumn('teacher_price_official');
            $table->dropColumn('insurance');
            $table->dropColumn('ndfl');
        });
    }
}
