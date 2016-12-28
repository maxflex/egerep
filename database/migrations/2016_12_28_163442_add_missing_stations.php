<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingStations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('stations')->insert([
            ['id' => 193, 'title' => 'Павелецкая',      'lat' => 55.7305, 'lng' => 37.6377, 'line_id' => 2, 'rel'=> '91'],
            ['id' => 194, 'title' => 'Третьяково',      'lat' => 55.7412, 'lng' => 37.6274, 'line_id' => 6, 'rel'=> '137,82'],
            ['id' => 195, 'title' => 'Китай-город',     'lat' => 55.7553, 'lng' => 37.6333, 'line_id' => 7, 'rel'=> '48'],
            ['id' => 196, 'title' => 'Белорусская',     'lat' => 55.7767, 'lng' => 37.5835, 'line_id' => 2, 'rel'=> '15'],
            ['id' => 197, 'title' => 'Каширская',       'lat' => 55.6551, 'lng' => 37.6487, 'line_id' => 2, 'rel'=> '46'],
            ['id' => 198, 'title' => 'Киевская',        'lat' => 55.7436, 'lng' => 37.5655, 'line_id' => 4, 'rel'=> '47,187'],
            ['id' => 199, 'title' => 'Таганская',       'lat' => 55.7402, 'lng' => 37.6522, 'line_id' => 7, 'rel'=> '68,131'],
        ]);
        \DB::table('stations')->insert([
            'id' => 186,
            'title' => 'Кунцевская',
            'line_id' => 3,
            'lat' => 55.7307,
            'lng' => 37.4459,
            'rel' => 62
        ], [
            'id' => 187,
            'title' => 'Киевская',
            'line_id' => 3,
            'lat' => 55.7442,
            'lng' => 37.5645,
            'rel' => '47,198'
        ], [
            'id' => 188,
            'title' => 'Курская',
            'line_id' => 3,
            'lat' => 55.7570,
            'lng' => 37.6595,
            'rel' => '63,158'
        ], [
            'id' => 189,
            'title' => 'Парк Культуры',
            'line_id' => 1,
            'lat' => 55.7356,
            'lng' => 37.5943,
            'rel' => 92
        ], [
            'id' => 190,
            'title' => 'Комсомольская',
            'line_id' => 1,
            'lat' => 55.7753,
            'lng' => 37.6562,
            'rel' => 51,
        ], [
            'id' => 191,
            'title' => 'Проспект Мира',
            'line_id' => 6,
            'lat' => 55.7798,
            'lng' => 37.6318,
            'rel' => 109
        ], [
            'id' => 192,
            'title' => 'Октябрьская',
            'line_id' => 6,
            'lat' => 55.7297,
            'lng' => 37.6091,
            'rel' => 86
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stations', function (Blueprint $table) {
            //
        });
    }
}
