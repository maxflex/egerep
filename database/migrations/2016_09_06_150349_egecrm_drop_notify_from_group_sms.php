<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmDropNotifyFromGroupSms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('group_sms', function (Blueprint $table) {
            $table->dropColumn('notified');
        });
        Schema::connection('egecrm')->table('group_sms', function (Blueprint $table) {
            $table->integer('year')->default(2015);
            $table->index('id_student');
            $table->index('id_branch');
            $table->index('id_subject');
            $table->index('first_schedule');
            $table->index('cabinet');
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_sms', function (Blueprint $table) {
            //
        });
    }
}
