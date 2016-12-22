<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewMetroStations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE graph_places MODIFY COLUMN `lines` SET('1','2','3','4','5','6','7','8','9','10','11','12','14')");

        DB::table('graph_places')
            ->insert([
                ['name' => 'Петровско-Разумовская', 'lines' => '10', 'rel'=>'', 'type' => 'metro'], //212
                ['name' => 'Фонвизинская',          'lines' => '10', 'rel'=>'', 'type' => 'metro'], //213
                ['name' => 'Бутырская',             'lines' => '10', 'rel'=>'97', 'type' => 'metro'], //214
                ['name' => 'Технопарк',             'lines' => '2', 'rel'=>'', 'type' => 'metro'], //215
                ['name' => 'Бульвар Рокоссовского', 'lines' => '1', 'rel'=>'225', 'type' => 'metro'], //216
                ['name' => 'Саларьево',             'lines' => '1', 'rel'=>'', 'type' => 'metro'], //217
                ['name' => 'Спартак',               'lines' => '7', 'rel'=>'', 'type' => 'metro'], //218
                ['name' => 'Балтийская',            'lines' => '14', 'rel'=>'29', 'type' => 'metro'], //219
                ['name' => 'Лихоборы',              'lines' => '14', 'rel'=>'', 'type' => 'metro'], //220
                ['name' => 'Владыкино',             'lines' => '14', 'rel'=>'27', 'type' => 'metro'], //221
                ['name' => 'Ботанический сад',      'lines' => '14', 'rel'=>'20', 'type' => 'metro'], //222
                ['name' => 'Ростокино',             'lines' => '14', 'rel'=>'', 'type' => 'metro'], //223
                ['name' => 'Локомотив',             'lines' => '14', 'rel'=>'154', 'type' => 'metro'], //224
                ['name' => 'Измайлово',             'lines' => '14', 'rel'=>'94', 'type' => 'metro'], //225
                ['name' => 'Соколиная гора',        'lines' => '14', 'rel'=>'', 'type' => 'metro'], //226
                ['name' => 'Шоссе Энтузиастов',     'lines' => '14', 'rel'=>'160', 'type' => 'metro'], //227
                ['name' => 'Нижегородская',         'lines' => '14', 'rel'=>'', 'type' => 'metro'], //228
                ['name' => 'Дубровка',              'lines' => '14', 'rel'=>'41', 'type' => 'metro'], //229
                ['name' => 'Автозаводская',         'lines' => '14', 'rel'=>'2', 'type' => 'metro'], //230
                ['name' => 'Площадь Гагарина',      'lines' => '14', 'rel'=>'65', 'type' => 'metro'], //231
                ['name' => 'Лужники',               'lines' => '14', 'rel'=>'125', 'type' => 'metro'], //232
                ['name' => 'Кутузовская',           'lines' => '14', 'rel'=>'64', 'type' => 'metro'], //233
                ['name' => 'Шелепиха',              'lines' => '14', 'rel'=>'', 'type' => 'metro'], //234
                ['name' => 'Панфиловская',          'lines' => '14', 'rel'=>'87', 'type' => 'metro'] //235
            ]);

        DB::table('stations')
            ->insert([
                ['title' => 'Петровско-Разумовская', 'line_id' => '10', 'lat'=> 55.8351, 'lng' => 37.5745], //212
                ['title' => 'Фонвизинская',          'line_id' => '10', 'lat'=> 55.8228, 'lng' => 37.5881], //213
                ['title' => 'Бутырская',             'line_id' => '10', 'lat'=> 55.8133, 'lng' => 37.6028], //214
                ['title' => 'Технопарк',             'line_id' => '2',  'lat'=> 55.6950, 'lng' => 37.6641], //215
                ['title' => 'Бульвар Рокоссовского', 'line_id' => '1',  'lat'=> 55.8172, 'lng' => 37.7369], //216
                ['title' => 'Саларьево',             'line_id' => '1',  'lat'=> 55.6219, 'lng' => 37.4242], //217
                ['title' => 'Спартак',               'line_id' => '7',  'lat'=> 55.8182, 'lng' => 37.4353], //218
                ['title' => 'Балтийская',            'line_id' => '14', 'lat'=> 55.8258, 'lng' => 37.4961], //219
                ['title' => 'Лихоборы',              'line_id' => '14', 'lat'=> 55.8472, 'lng' => 37.5513], //220
                ['title' => 'Владыкино',             'line_id' => '14', 'lat'=> 55.8472, 'lng' => 37.5919], //221
                ['title' => 'Ботанический сад',      'line_id' => '14', 'lat'=> 55.8456, 'lng' => 37.6403], //222
                ['title' => 'Ростокино',             'line_id' => '14', 'lat'=> 55.8394, 'lng' => 37.6678], //223
                ['title' => 'Локомотив',             'line_id' => '14', 'lat'=> 55.8039, 'lng' => 37.746], //224
                ['title' => 'Измайлово',             'line_id' => '14', 'lat'=> 55.7886, 'lng' => 37.7428], //225
                ['title' => 'Соколиная гора',        'line_id' => '14', 'lat'=> 55.77, 	 'lng' => 37.7453], //226
                ['title' => 'Шоссе Энтузиастов',     'line_id' => '14', 'lat'=> 55.7590, 'lng' => 37.7463], //227
                ['title' => 'Нижегородская',         'line_id' => '14', 'lat'=> 55.7322, 'lng' => 37.7282], //228
                ['title' => 'Дубровка',              'line_id' => '14', 'lat'=> 55.7127, 'lng' => 37.6778], //229
                ['title' => 'Автозаводская',         'line_id' => '14', 'lat'=> 55.7063, 'lng' => 37.6631], //230
                ['title' => 'Площадь Гагарина',      'line_id' => '14', 'lat'=> 55.7069, 'lng' => 37.5858], //231
                ['title' => 'Лужники',               'line_id' => '14', 'lat'=> 55.7203, 'lng' => 37.5631], //232
                ['title' => 'Кутузовская',           'line_id' => '14', 'lat'=> 55.7399, 'lng' => 37.5344], //233
                ['title' => 'Шелепиха',              'line_id' => '14', 'lat'=> 55.7575, 'lng' => 37.5256], //234
                ['title' => 'Панфиловская',          'line_id' => '14', 'lat'=> 55.7980, 'lng' => 37.4998] //235
            ]);
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
