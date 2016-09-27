<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAnotherCabinetDrop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('groups', function (Blueprint $table) {
//            $table->dropColumn('id_branch');
//            $table->dropColumn('cabinet');
        });
        Schema::connection('egecrm')->table('group_sms', function (Blueprint $table) {
//            $table->dropColumn('id_branch');
        });

        $data = \DB::connection('egecrm')->table('group_time')->get();
        foreach($data as $d) {
            $obj = \DB::connection('egecrm')->table('time')->whereId($d->id_time)->first();
            $query = \DB::connection('egecrm')->table('group_schedule')->whereRaw("DAYOFWEEK(date) = {$obj->day} AND time='{$obj->time}:00' AND cabinet > 0");
            if ($query->exists()) {
                \DB::connection('egecrm')->table('group_time')->where('id_group', $d->id_group)->where('id_time', $d->id_time)->update([
                    'id_cabinet' => $query->value('cabinet')
                ]);
            }
        }
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
