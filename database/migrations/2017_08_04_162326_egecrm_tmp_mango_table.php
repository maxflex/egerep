<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmTmpMangoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('mango', function (Blueprint $table) {
            $table->boolean('status_1')->index();
            $table->boolean('status_2')->index();
            $table->boolean('status_3')->index();
            $table->string('additional')->nullable();
        });

        // перенос статусов
        $statuses = dbEgecrm('call_statuses')->get();
        foreach($statuses as $status) {
            dbEgecrm('mango')->whereId($status->id)->update([
                "status_{$status->status}" => 1,
                "additional" => $status->additional,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mango', function (Blueprint $table) {
            //
        });
    }
}
