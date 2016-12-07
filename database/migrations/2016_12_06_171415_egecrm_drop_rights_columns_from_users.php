<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmDropRightsColumnsFromUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('users', function (Blueprint $table) {
            $table->dropColumn('show_contract');
            $table->dropColumn('show_attachments');
            $table->dropColumn('show_summary');
            $table->dropColumn('remove_requests');
            $table->dropColumn('show_accounts');
            $table->dropColumn('show_debt');
            $table->dropColumn('is_seo');
            $table->dropColumn('is_dev');
            $table->dropColumn('show_tasks');
            $table->dropColumn('can_approve_tutor');
            $table->dropColumn('show_phone_calls');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
