<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmRefactor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('email', function (Blueprint $table) {
            $table->dropColumn('place');
            $table->dropColumn('id_place');
            $table->dropColumn('additional');
        });

        Schema::connection('egecrm')->table('sms', function (Blueprint $table) {
            $table->dropColumn('place');
            $table->dropColumn('id_place');
            $table->dropColumn('additional');
            $table->dropColumn('force_ok');
        });

        Schema::connection('egecrm')->drop('group_student_statuses');
        Schema::connection('egecrm')->drop('group_teacher_statuses');

        Schema::connection('egecrm')->table('payments', function (Blueprint $table) {
            $table->dropColumn('deleted');
        });

        Schema::connection('egecrm')->table('requests', function (Blueprint $table) {
            $table->dropColumn('id_first_save_user');
            $table->dropColumn('first_save_date');
            $table->dropColumn('delay_time');
        });

        Schema::connection('egecrm')->table('students', function (Blueprint $table) {
            $table->dropColumn('other_info');
            $table->dropColumn('minimized');
            $table->dropColumn('code');
        });

        Schema::connection('egecrm')->table('stations', function (Blueprint $table) {
            $table->dropColumn('short');
        });

        Schema::connection('egecrm')->table('teacher_reviews', function (Blueprint $table) {
            $table->dropColumn('date');
            $table->renameColumn('code', 'score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
