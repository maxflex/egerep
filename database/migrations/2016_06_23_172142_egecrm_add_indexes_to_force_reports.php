<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAddIndexesToForceReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('reports_force', function (Blueprint $table) {
            $table->index('id_student');
            $table->index('id_teacher');
            $table->index('id_subject');
            $table->index('year');
            $table->unique(['id_student', 'id_teacher', 'id_subject', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports_force', function (Blueprint $table) {
            //
        });
    }
}
