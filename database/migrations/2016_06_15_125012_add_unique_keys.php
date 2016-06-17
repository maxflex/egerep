<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unique('attachment_id');
        });
        Schema::table('archives', function (Blueprint $table) {
            $table->unique('attachment_id');
        });
        Schema::table('attachments', function (Blueprint $table) {
            $table->unique(['tutor_id','client_id']);
        });


        $query = Tutor::query();

        if ($query->exists()) {
            echo 1;
        }

        $query->where('id', 1)->get();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            //
        });
    }
}
