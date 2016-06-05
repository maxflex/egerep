<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneDuplicatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_duplicates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('entity_type', 100)->index();
            $table->string('phone', 20)->index();
            $table->unique(['phone', 'entity_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('phone_duplicates');
    }
}
