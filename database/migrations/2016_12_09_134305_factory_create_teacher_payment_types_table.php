<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FactoryCreateTeacherPaymentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('factory')->create('teacher_payment_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
        });

        dbFactory('teacher_payment_types')->insert([
            ['title' => 'личная встреча'],
            ['title' => 'карта Сбербанка'],
            ['title' => 'Яндекс.Деньги']
        ]);

        Schema::create('planned_accounts', function (Blueprint $table) {
            $table->tinyInteger('payment_method')->unsigned()->default(1)->change();
        });

        \DB::table('planned_accounts')->update(['payment_method' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('factory')->drop('teacher_payment_types');
    }
}
