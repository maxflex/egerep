<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddErrorsToSettingsUpdating extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('settings')->insert(['key' => 'request_errors_updating', 'value' => '0']);
        \DB::table('settings')->insert(['key' => 'request_errors_updated', 'value' => now()]);
        \DB::table('settings')->insert(['key' => 'account_errors_updating', 'value' => '0']);
        \DB::table('settings')->insert(['key' => 'account_errors_updated', 'value' => now()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
