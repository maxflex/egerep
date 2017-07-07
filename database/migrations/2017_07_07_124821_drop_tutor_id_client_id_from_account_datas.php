<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTutorIdClientIdFromAccountDatas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_datas', function (Blueprint $table) {
            $table->integer('attachment_id')->unsigned();
        });

        \DB::statement('update account_datas ad
                        join attachments a on (ad.client_id = a.client_id and ad.tutor_id = a.tutor_id)
                        set ad.attachment_id = a.id');

        Schema::table('account_datas', function (Blueprint $table) {
            $table->foreign('attachment_id')->references('id')->on('attachments')->onDelete('cascade');
        });

        // Schema::table('account_datas', function (Blueprint $table) {
        //     $table->dropColumn('tutor_id');
        //     $table->dropColumn('client_id');
        //     $table->dropForeign(['tutor_id']);
        //     $table->dropForeign(['client_id']);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_datas', function (Blueprint $table) {
            //
        });
    }
}
