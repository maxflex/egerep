<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTestingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->drop('testing');
        Schema::connection('egecrm')->drop('testing_students');
        \DB::connection('egecrm')->table('comments')->where('place', 'TESTING')->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('testing', function (Blueprint $table) {
            //
        });
    }
}
