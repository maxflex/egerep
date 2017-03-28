<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropDebtCalcFromEverywhere extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->dropColumn('debt_calc');
        });
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('debt_calc');
        });
        \DB::table('settings')->whereKey('debt_table_updated')->delete();
        \DB::table('settings')->whereKey('debts_sum')->update(['key' => 'debt_sum']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tutors', function (Blueprint $table) {
            //
        });
    }
}
