<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class ConnectStations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'connect_stations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $tutors = DB::table('tutors')->join('tutor_data', 'tutor_data.tutor_id', '=', 'tutors.id')
            ->whereRaw("length(svg_map) < 749 AND (tutors.public_desc <> '' OR tutors.state = 5)")
            // ->where('tutors.id', 6)
            ->select('tutors.id', 'tutor_data.svg_map')->orderBy('id', 'asc')->get();

        $stations = collect(DB::table('stations')->select('id', 'title')->get())->keyBy('id');

        $bar = $this->output->createProgressBar(count($tutors));
        $text = '';

        foreach($tutors as $tutor) {
            $svg_map = explode(',', $tutor->svg_map);

            // неотмеченные соседи
            $unchecked_neighbours = [];

            // какие станции надо добавить
            $new_station_ids = [];

            foreach($svg_map as $station_id) {
                $neighbours = DB::table('graph_distances')->where('from', $station_id)->orWhere('to', $station_id)->get();
                foreach($neighbours as $neighbour) {
                    // соседская станция
                    $neighbour_station_id = $neighbour->from == $station_id ? $neighbour->to : $neighbour->from;

                    // если соседская станция отмечена – пропускаем
                    if (in_array($neighbour_station_id, $svg_map)) {
                        continue;
                    }

                    // if ($neighbour_station_id == 87) {
                    //     $this->error($station_id);
                    // }

                    // если соседская станция отсутствует
                    // если соседская станция уже была добавляена в отсутствующие
                    if (in_array($neighbour_station_id, $unchecked_neighbours)) {
                        // добавляем в новые
                        $new_station_ids[] = $neighbour_station_id;
                    } else {
                        $unchecked_neighbours[] = $neighbour_station_id;
                    }
                }
            }

            if (count($new_station_ids)) {
                $text .= "Tutor: {$tutor->id} \n" . implode(', ', array_map(function($station_id) use ($stations) {
                    return $stations[$station_id]->title;
                }, $new_station_ids)) . "\n=======================\n";

                // $this->info("Tutor {$tutor->id}: ");
                // $this->line(implode(', ', $new_station_ids));
                // $this->line("\n\n");
                $svg_map = array_merge($svg_map, $new_station_ids);
                sort($svg_map);
                // DB::table('tutor_data')->where('tutor_id', $tutor->id)->update([
                //     'svg_map' => implode(',', $svg_map)
                // ]);
            }
        }

        \Storage::put('stations.txt', $text);
    }
}
