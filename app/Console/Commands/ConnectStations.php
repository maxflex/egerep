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
            // ->where('tutors.id', 3)
            ->select('tutors.id', 'tutor_data.svg_map')->orderBy('id', 'asc')->get();

        $distances = collect(DB::table('graph_distances')->get());

        // $stations = collect(DB::table('stations')->select('id', 'title')->get())->keyBy('id');

        $bar = $this->output->createProgressBar(count($tutors));
        $text = '';

        foreach($tutors as $tutor) {
            $bar->advance();
            $svg_map = explode(',', $tutor->svg_map);
            $added = false;
            $checked = [];

            foreach($svg_map as $station_1) {
                if ($added) {
                    break;
                }
                foreach($svg_map as $station_2) {
                    if ($added) {
                        break;
                    }
                    if ($station_1 != $station_2 && ! in_array(md5(min($station_1, $station_2) . max($station_1, $station_2)), $checked)) {
                        $checked[] = md5(min($station_1, $station_2) . max($station_1, $station_2));
                        if (! $this->hasPath($station_1, $station_2, $distances, $svg_map)) {
                            $text .= $tutor->id . "\n";
                            // $this->info($tutor->id);
                            $added = true;
                        }
                    }
                }
            }
        }
        //  $bar->finish();

        \Storage::put('stations.txt', $text);
    }

    /**
    * Create two sets of nodes:  toDoSet and doneSet
    * Add the source node to the toDoSet
    * while (toDoSet is not empty) {
    *   Remove the first element from toDoList
    *   Add it to doneList
    *   foreach (node reachable from the removed node) {
    *     if (the node equals the destination node) {
    *        return success
    *     }
    *     if (the node is not in doneSet) {
    *        add it to toDoSet
    *     }
    *   }
    * }

    * return failure.
    */
    private function hasPath($station_1, $station_2, $distances, $svg_map)
    {
        // $this->error($station_2);
        $todo = [$station_1];
        $done = [];
        // $checked = [];
        // $step = 1;

        // while (! empty($todo) && $step < 100) {
        while (! empty($todo)) {
            // $step++;
            // $this->info(implode(', ', $todo) . "  | " . implode(', ', $done));
            $node = array_shift($todo);
            // if (in_array($node, $done)) {
            //     continue;
            // }
            $done[] = $node;

            // $int = array_intersect($todo, $done);
            // if (count($int)) {
            //     dump($int);
            //     exit();
            // }

            // dump($this->reachableNodes($distances, $svg_map, $node));
            foreach($this->reachableNodes($distances, $svg_map, $node) as $reachable_node) {
                if ($reachable_node == $station_2) {
                    return true;
                }
                if (! in_array($reachable_node, $done) && ! in_array($reachable_node, $todo)) {
                    $todo[] = $reachable_node;
                }
            }
        }

        return false;
    }

    private function reachableNodes($distances, $svg_map, $node)
    {
        $return = [];

        foreach($distances as $distance) {
            if ($distance->from == $node) {
                if (in_array($distance->to, $svg_map)) {
                    $return[] = $distance->to;
                }
            } else
            if ($distance->to == $node) {
                if (in_array($distance->from, $svg_map)) {
                    $return[] = $distance->from;
                }
            }
        }

        return $return;
    }
}
