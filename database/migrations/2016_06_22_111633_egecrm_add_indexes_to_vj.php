<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmAddIndexesToVj extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->table('visit_journal', function (Blueprint $table) {
            $table->index('id_entity');
            $table->index('type_entity');
            $table->index('id_teacher');
            $table->index('id_subject');
            $table->index('year');
            $table->index('lesson_date');
        });

        Schema::connection('egecrm')->table('reports', function (Blueprint $table) {
            $table->index('id_student');
            $table->index('id_teacher');
            $table->index('id_subject');
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visit_journal', function (Blueprint $table) {
            //
        });
    }
}
