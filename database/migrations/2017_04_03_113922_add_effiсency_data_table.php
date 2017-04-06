<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEffiсencyDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('efficency_data', function(Blueprint $table) {
            $table->increments('id');
            $table->date('date')->index();
            $table->integer('user_id')->index();

            $table->integer('requests_new');
            $table->integer('requests_awaiting');
            $table->integer('requests_checked_reasoned_deny');
            $table->integer('requests_deny');
            $table->integer('requests_finished');
            $table->integer('requests_reasoned_deny');
            $table->integer('requests_total');

            $table->integer('attachments_newest');
            $table->integer('attachments_active');
            $table->integer('attachments_archived_no_lessons');
            $table->integer('attachments_archived_one_lesson');
            $table->integer('attachments_archived_two_lessons');
            $table->integer('attachments_archived_three_or_more_lessons');
            $table->integer('attachments_total');

            $table->integer('forecast');

            $table->float('conversion_denominator');

            //$table->integer('commission');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('efficency_data');
    }
}
