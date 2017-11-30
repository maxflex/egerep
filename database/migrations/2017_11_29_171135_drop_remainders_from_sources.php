<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropRemaindersFromSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sources = \DB::table('payment_sources')->get();
        foreach($sources as $source) {
            \DB::table('payment_source_remainders')->insert([
                'source_id' => $source->id,
                'date' => $source->remainder_date,
                'remainder' => $source->remainder
            ]);
        }

        Schema::table('payment_sources', function (Blueprint $table) {
            $table->dropColumn('remainder_date');
            $table->dropColumn('remainder');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_sources', function (Blueprint $table) {
            //
        });
    }
}
