<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Station;

class GraphRecalc extends Command
{
    static $places;
    static $places_count;
    static $mass; //матрица смежностей
    static $distances; //масств расстояний
    static $parents; //массив предков
    static $ps; //множество вершин

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'graph:recalc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate graphs and regenerate distances table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        static::loadPlaces();
        static::loadMass();

        \DB::table('distances')->truncate();
        for ($i = 0; $i < static::$places_count; $i++) {
            $start = static::$places[$i];
            static::deicstra($start);
            for ($j = $i; $j < static::$places_count; $j++) {
                $finish = static::$places[$j];
                $dist = static::$distances[$finish];
                static::updateDistance($start, $finish, $dist);
            }
        }


        $this->info('Graphs recalculated');
    }

    private static function loadPlaces() {
        static::$places = \DB::table('stations')->pluck('id');
        static::$places_count = count(static::$places);
    } //загрузка станций

    private static function loadMass() {
        static::$mass = [];
        foreach (static::$places as $i) {
            static::$mass[$i] = [];
            foreach (static::$places as $j) {
                static::$mass[$i][$j] = PHP_INT_MAX;
            }
        } //заполнили бесконечностью
        $distances = \DB::table('graph_distances')->get();
        foreach ($distances as $distance) {
            static::$mass[$distance->from][$distance->to] = $distance->distance;
            static::$mass[$distance->to][$distance->from] = $distance->distance;
        } //заполнили матрицу смежностей
    } //построение матрицы смежностей

    private static function initDeicstra($start) {
        for ($i = 0; $i < static::$places_count; $i++) {
            $id = static::$places[$i];
            static::$ps[$id] = true;
            static::$distances[$id] = static::$mass[$start][$id];
            static::$parents[$id] = $id;
        }
        static::$distances[$start] = 0;
        static::$parents[$start] = -1;
    } //подготовка к запуску дейкстры

    private static function fmin() {
        $m = PHP_INT_MAX;
        $im = -1;
        for ($i = 0; $i < static::$places_count; $i++) {
            $id = static::$places[$i];
            if (isset(static::$ps[$id])) {
                if (static::$distances[$id] <= $m) {
                    $m = static::$distances[$id];
                    $im = $id;
                }
            }
        }
        return $im;
    } //поиск вершины с наименьшим расстоянием

    private static function deicstra($start) {
        static::initDeicstra($start);
        while (count(static::$ps) > 0) { //пока не просмотрим все вершины
            $u = static::fmin(); //находим вершину с мин расстоянием
            unset(static::$ps[$u]); //удаляем из множества
            for ($i = 0; $i < static::$places_count; $i++) {
                $id = static::$places[$i];
                if (isset(static::$ps[$id])) {
                    if (static::$distances[$id] >= static::$distances[$u] + static::$mass[$u][$id]) {
                        //улучшаем результат
                        static::$distances[$id] = static::$distances[$u] + static::$mass[$u][$id];
                        static::$parents[$id] = $u;
                    }
                }
            }
        }
    } //алгоритм Дейкстры

    private static function updateDistance($from, $to, $distance) {
        // если точка назначения равна точке отправления,
        // то не считать
        if ($from == $to) {
            return;
        }
        $p1 = min($from, $to);
        $p2 = max($from, $to);
        \DB::insert("INSERT INTO distances (`from`, `to`, `distance`) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE distance = VALUES(distance)", [$p1, $p2, $distance]);
        // не дублируем точки – update 2017 – надо дублировать точки, иначе на egerep-web неправильно сортирует по удаленности от метро
        \DB::insert("INSERT INTO distances (`from`, `to`, `distance`) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE distance = VALUES(distance)", [$p2, $p1, $distance]);
    }
}
