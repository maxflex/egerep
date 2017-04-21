<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmDropGroupExternalLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // GroupLevel::EXTERNAL = 5
        // Grades::EXTERNAL = 14

        $group_ids = dbEgecrm('groups')->where('level', 5)->pluck('id');
        // dbEgecrm('visit_journal')->whereIn('id_group', $group_ids)->update(['grade' => 14]);
        dbEgecrm('groups')->whereIn('id', $group_ids)->where('level', 5)->update(['grade' => 14, 'level' => 0]);
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
