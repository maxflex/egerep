<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmTransformDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        dbEgecrm('passports')->update([
            'date_birthday' => DB::raw("STR_TO_DATE(date_birthday, '%d.%m.%Y')"),
            'date_issued' => DB::raw("STR_TO_DATE(date_issued, '%d.%m.%Y')"),
        ]);

        // запустить вручную
        // Schema::connection('egecrm')->table('passports', function (Blueprint $table) {
        //     $table->date('date_birthday')->nullable()->change();
        //     $table->date('date_issued')->nullable()->change();
        // });

        dbEgecrm('contracts')->update(['date' => DB::raw("STR_TO_DATE(date, '%d.%m.%Y')")]);
        Schema::connection('egecrm')->table('contracts', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
        });

        dbEgecrm('contracts_test')->update(['date' => DB::raw("STR_TO_DATE(date, '%d.%m.%Y')")]);
        Schema::connection('egecrm')->table('contracts_test', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
        });

        dbEgecrm('payments')->update(['date' => DB::raw("STR_TO_DATE(date, '%d.%m.%Y')")]);

        // ВРУЧНУЮ
        // Schema::connection('egecrm')->table('payments', function (Blueprint $table) {
        //     $table->date('date')->nullable()->change();
        // });


        dbEgecrm('reports')->update(['date' => DB::raw("STR_TO_DATE(date, '%d.%m.%Y')")]);
        Schema::connection('egecrm')->table('reports', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
        });

        dbEgecrm('exam_days')->update(['date' => DB::raw("STR_TO_DATE(date, '%d.%m.%Y')")]);
        Schema::connection('egecrm')->table('exam_days', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('passports', function (Blueprint $table) {
            //
        });
    }
}
