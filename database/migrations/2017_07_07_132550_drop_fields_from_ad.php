<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropFieldsFromAd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_datas', function (Blueprint $table) {
            // $table->dropForeign('account_datas_tutor_id_foreign');
            // $table->dropIndex('client_id');
            $table->dropColumn('tutor_id');
            $table->dropColumn('client_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_datas', function (Blueprint $table) {
            //
        });
    }
}
