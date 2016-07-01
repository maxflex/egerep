<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAddIndexesToGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('groups', function (Blueprint $table) {
            $table->index('id_teacher');
            $table->index('id_subject');
            $table->index('id_branch');
            $table->index('year');
            $table->index('ended');
        });
        Schema::connection('egecrm')->table('group_time', function (Blueprint $table) {
            $table->index('id_group');
            $table->index('day');
            $table->index('time');
        });
        Schema::connection('egecrm')->table('group_schedule', function (Blueprint $table) {
            $table->index('is_free');
            $table->index('id_group');
            $table->index('cancelled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            //
        });
    }
}
