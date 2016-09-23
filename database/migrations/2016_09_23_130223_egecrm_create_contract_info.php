<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmCreateContractInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->create('contract_info', function (Blueprint $table) {
            $table->integer('id_contract')->unsigned()->primary();
            $table->integer('grade')->unsigned()->index();
            $table->integer('id_student')->unsigned()->index();
            $table->integer('year')->unsigned()->index();
        });
        $data = \DB::connection('egecrm')->table('contracts')->whereRaw('id=id_contract')->get();
        foreach($data as $d) {
            \DB::connection('egecrm')->table('contract_info')->insert([
                'id_contract' => $d->id,
                'grade'       => $d->grade,
                'id_student'  => $d->id_student,
                'year'        => $d->year,
            ]);
        }
        Schema::connection('egecrm')->table('contracts', function (Blueprint $table) {
            $table->dropColumn('id_contract');
            $table->dropColumn('grade');
            $table->dropColumn('id_student');
            $table->dropColumn('year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('contract_info');
    }
}
