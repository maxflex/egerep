<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TeacherProfileUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('teachers', function (Blueprint $table) {
			$table->string('phone4', 255)->after('phone3');
			$table->string('phone_comment', 64)->after('phone');
			$table->string('phone2_comment', 64)->after('phone2');
			$table->string('phone3_comment', 64)->after('phone3');
			$table->string('phone4_comment', 64)->after('phone4');
			$table->string('email_comment', 64)->after('email');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            //
        });
    }
}
