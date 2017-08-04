<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SpeedUpTmpMango extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('mango', function (Blueprint $table) {
            $table->index('line_number');
            $table->index(['from_extension', 'answer']);
            $table->index(['to_extension', 'answer']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mango', function (Blueprint $table) {
            //
        });
    }
}
