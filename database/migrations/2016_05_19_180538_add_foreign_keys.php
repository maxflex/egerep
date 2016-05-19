<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->foreign('tutor_id')->references('id')->on('tutors')->onDelete('cascade');
        });

        Schema::table('account_datas', function ($table) {
            $table->foreign('tutor_id')->references('id')->on('tutors')->onDelete('cascade');
        });

        Schema::table('attachments', function ($table) {
            $table->foreign('tutor_id')->references('id')->on('tutors')->onDelete('cascade');
        });
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
