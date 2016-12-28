<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropGraphPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->string('rel', 15);
        });

        foreach (\DB::table('graph_places')->get() as $place) {
            \DB::table('stations')
                ->whereId($place->id)
                ->update([
                    'rel' => $place->rel
                ]);
        }

        Schema::drop('graph_places');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stations', function (Blueprint $table) {
            //
        });
    }
}
