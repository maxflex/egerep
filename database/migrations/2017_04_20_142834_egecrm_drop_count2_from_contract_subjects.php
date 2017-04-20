<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmDropCount2FromContractSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::connection('egecrm')->statement('update contract_subjects set count=(count + count2)');
        Schema::connection('egecrm')->table('contract_subjects', function (Blueprint $table) {
            $table->dropColumn('count2');
        });
        Schema::connection('egecrm')->table('contracts', function (Blueprint $table) {
            $table->tinyInteger('payments_split')->unsigned()->default(0);
            $table->tinyInteger('payments_queue')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract_subjects', function (Blueprint $table) {
            //
        });
    }
}
