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
        DB::statement("ALTER TABLE graph_places MODIFY COLUMN `lines` SET('1','2','3','4','5','6','7','8','9','10','11','12','13')");

        DB::table('graph_places')
            ->insert([
                ['name' => 'Петровско-Разумовская', 'lines' => '10', 'rel'=>'97',   'type' => 'metro'], //212
                ['name' => 'Фонвизинская',          'lines' => '10', 'rel'=>'',     'type' => 'metro'], //213
                ['name' => 'Бутырская',             'lines' => '10', 'rel'=>'',     'type' => 'metro'], //214
                ['name' => 'Технопарк',             'lines' => '2',  'rel'=>'',     'type' => 'metro'], //215
                ['name' => 'Бульвар Рокоссовского', 'lines' => '1',  'rel'=>'',     'type' => 'metro'], //216
                ['name' => 'Саларьево',             'lines' => '1',  'rel'=>'',     'type' => 'metro'], //217
                ['name' => 'Спартак',               'lines' => '7',  'rel'=>'',     'type' => 'metro'], //218
                ['name' => 'Балтийская',            'lines' => '13', 'rel'=>'29',   'type' => 'metro'], //219
                ['name' => 'Лихоборы',              'lines' => '13', 'rel'=>'',     'type' => 'metro'], //220
                ['name' => 'Владыкино',             'lines' => '13', 'rel'=>'27',   'type' => 'metro'], //221
                ['name' => 'Ботанический сад',      'lines' => '13', 'rel'=>'20',   'type' => 'metro'], //222
                ['name' => 'Ростокино',             'lines' => '13', 'rel'=>'',     'type' => 'metro'], //223
                ['name' => 'Локомотив',             'lines' => '13', 'rel'=>'154',  'type' => 'metro'], //224
                ['name' => 'Измайлово',             'lines' => '13', 'rel'=>'94',   'type' => 'metro'], //225
                ['name' => 'Соколиная гора',        'lines' => '13', 'rel'=>'',     'type' => 'metro'], //226
                ['name' => 'Шоссе Энтузиастов',     'lines' => '13', 'rel'=>'160',  'type' => 'metro'], //227
                ['name' => 'Нижегородская',         'lines' => '13', 'rel'=>'',     'type' => 'metro'], //228
                ['name' => 'Дубровка',              'lines' => '13', 'rel'=>'41',   'type' => 'metro'], //229
                ['name' => 'Автозаводская',         'lines' => '13', 'rel'=>'2',    'type' => 'metro'], //230
                ['name' => 'Площадь Гагарина',      'lines' => '13', 'rel'=>'65',   'type' => 'metro'], //231
                ['name' => 'Лужники',               'lines' => '13', 'rel'=>'125',  'type' => 'metro'], //232
                ['name' => 'Кутузовская',           'lines' => '13', 'rel'=>'64',   'type' => 'metro'], //233
                ['name' => 'Шелепиха',              'lines' => '13', 'rel'=>'',     'type' => 'metro'], //234
                ['name' => 'Панфиловская',          'lines' => '13', 'rel'=>'87',   'type' => 'metro']  //235
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
                ['title' => 'Балтийская',            'line_id' => '13', 'lat'=> 55.8258, 'lng' => 37.4961], //219
                ['title' => 'Лихоборы',              'line_id' => '13', 'lat'=> 55.8472, 'lng' => 37.5513], //220
                ['title' => 'Владыкино',             'line_id' => '13', 'lat'=> 55.8472, 'lng' => 37.5919], //221
                ['title' => 'Ботанический сад',      'line_id' => '13', 'lat'=> 55.8456, 'lng' => 37.6403], //222
                ['title' => 'Ростокино',             'line_id' => '13', 'lat'=> 55.8394, 'lng' => 37.6678], //223
                ['title' => 'Локомотив',             'line_id' => '13', 'lat'=> 55.8039, 'lng' => 37.746], //224
                ['title' => 'Измайлово',             'line_id' => '13', 'lat'=> 55.7886, 'lng' => 37.7428], //225
                ['title' => 'Соколиная гора',        'line_id' => '13', 'lat'=> 55.77, 	 'lng' => 37.7453], //226
                ['title' => 'Шоссе Энтузиастов',     'line_id' => '13', 'lat'=> 55.7590, 'lng' => 37.7463], //227
                ['title' => 'Нижегородская',         'line_id' => '13', 'lat'=> 55.7322, 'lng' => 37.7282], //228
                ['title' => 'Дубровка',              'line_id' => '13', 'lat'=> 55.7127, 'lng' => 37.6778], //229
                ['title' => 'Автозаводская',         'line_id' => '13', 'lat'=> 55.7063, 'lng' => 37.6631], //230
                ['title' => 'Площадь Гагарина',      'line_id' => '13', 'lat'=> 55.7069, 'lng' => 37.5858], //231
                ['title' => 'Лужники',               'line_id' => '13', 'lat'=> 55.7203, 'lng' => 37.5631], //232
                ['title' => 'Кутузовская',           'line_id' => '13', 'lat'=> 55.7399, 'lng' => 37.5344], //233
                ['title' => 'Шелепиха',              'line_id' => '13', 'lat'=> 55.7575, 'lng' => 37.5256], //234
                ['title' => 'Панфиловская',          'line_id' => '13', 'lat'=> 55.7980, 'lng' => 37.4998] //235
            ]);

        DB::table('graph_distances')
            ->insert([
                ['from' => 212, 'to' => 97,   'distance' => 1],
                ['from' => 212, 'to' => 213,  'distance' => 2],
                ['from' => 213, 'to' => 214,  'distance' => 3],
                ['from' => 215, 'to' => 2,    'distance' => 2],
                ['from' => 215, 'to' => 50,   'distance' => 2],
                ['from' => 216, 'to' => 154,  'distance' => 3],
                ['from' => 217, 'to' => 210,  'distance' => 2],
                ['from' => 218, 'to' => 162,  'distance' => 3],
                ['from' => 219, 'to' => 29,   'distance' => 12],
                ['from' => 219, 'to' => 220,  'distance' => 5],
                ['from' => 219, 'to' => 235,  'distance' => 5],
                ['from' => 220, 'to' => 235,  'distance' => 5],
                ['from' => 220, 'to' => 221,  'distance' => 5],
                ['from' => 221, 'to' => 222,  'distance' => 3],
                ['from' => 221, 'to' => 27,   'distance' => 7],
                ['from' => 222, 'to' => 20,   'distance' => 8],
                ['from' => 222, 'to' => 223,  'distance' => 3],
                ['from' => 223, 'to' => 224,  'distance' => 8],
                ['from' => 223, 'to' => 154,  'distance' => 5],
                ['from' => 224, 'to' => 225,  'distance' => 2],
                ['from' => 225, 'to' => 94,   'distance' => 9],
                ['from' => 225, 'to' => 226,  'distance' => 3],
                ['from' => 226, 'to' => 227,  'distance' => 3],
                ['from' => 227, 'to' => 160,  'distance' => 8],
                ['from' => 227, 'to' => 228,  'distance' => 5],
                ['from' => 228, 'to' => 229,  'distance' => 7],
                ['from' => 229, 'to' => 41,   'distance' => 12],
                ['from' => 229, 'to' => 230,  'distance' => 2],
                ['from' => 230, 'to' => 231,  'distance' => 10],
                ['from' => 230, 'to' => 2,    'distance' => 10],
                ['from' => 231, 'to' => 232,  'distance' => 4],
                ['from' => 231, 'to' => 65,   'distance' => 6],
                ['from' => 232, 'to' => 125,  'distance' => 8],
                ['from' => 232, 'to' => 233,  'distance' => 3],
                ['from' => 233, 'to' => 64,   'distance' => 6],
                ['from' => 233, 'to' => 234,  'distance' => 4],
                ['from' => 234, 'to' => 235,  'distance' => 8],
                ['from' => 235, 'to' => 87,   'distance' => 13]
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('graph_places')
            ->whereIn('id', range(212, 235))
            ->delete();

        DB::statement("alter table graph_places auto_increment = 212");

        DB::table('stations')
            ->whereIn('id', range(212, 235))
            ->delete();

        DB::statement("alter table stations auto_increment = 212");

        DB::table('graph_distances')
            ->whereIn('from', range(212, 235))
            ->delete();

        DB::table('graph_distances')
            ->whereIn('to', range(212, 235))
            ->delete();
    }
}
