<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EgecrmRenameStudentFreetimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('egecrm')->rename('students_freetime', 'freetime');
        Schema::connection('egecrm')->table('freetime', function (Blueprint $table) {
            $table->renameColumn('id_student', 'id_entity');
            $table->string('type_entity');
            $table->index('type_entity');
            $table->index('id_entity');
            $table->unique(['id_entity', 'type_entity', 'day', 'time_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students_freetime', function (Blueprint $table) {
            //
        });
    }
}
