<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIpToLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->string('ip')->nullable();
        });
        Schema::connection('egecrm')->table('logs', function (Blueprint $table) {
            $table->string('ip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn('ip');
        });
        Schema::connection('egecrm')->table('logs', function (Blueprint $table) {
            $table->dropColumn('ip');
        });
    }
}
