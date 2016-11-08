<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransferPaymentMethods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('accounts')->whereIn('payment_method', [1, 3])->update(['payment_method' => 0]);
        \DB::table('accounts')->where('payment_method', 2)->update(['payment_method' => 1]);
        \DB::table('accounts')->where('payment_method', 4)->update(['payment_method' => 2]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            //
        });
    }
}
