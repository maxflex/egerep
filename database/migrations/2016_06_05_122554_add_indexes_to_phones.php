<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Traits\Person;

class AddIndexesToPhones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            foreach (Person::$phone_fields as $phone_field) {
                $table->index($phone_field);
            }
        });
        Schema::table('tutors', function (Blueprint $table) {
            foreach (Person::$phone_fields as $phone_field) {
                $table->index($phone_field);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(Person::$phone_fields);
        });
        Schema::table('tutors', function (Blueprint $table) {
            $table->dropIndex(Person::$phone_fields);
        });
    }
}
