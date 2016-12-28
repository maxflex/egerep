<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class SvgAddStations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'svg:add_stations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new svg stations';

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
        # Step 1
        self::rule([69, 40],      [212, 213, 214]);
        self::rule([61,115,35],   [205,206,211]);
        self::rule([53],          [204]);
        self::rule([148,108,164], [209,210,217]);
        self::rule([141,162],     [218]);
        self::rule([2,50],        [215]);

        # Step 2
        $mck_station_ids = DB::table('stations')->where('line_id', 13)->pluck('id');
        $stations = DB::table('stations')->whereIn('rel', $mck_station_ids)->get();
        foreach($stations as $station) {
            $tutor_ids = DB::table('tutor_departures')->where('station_id', $station->id)->pluck('tutor_id');
            foreach($tutor_ids as $tutor_id) {
                try {
                    DB::table('tutor_departures')->insert([
                        'tutor_id' => $tutor_id,
                        'station_id' => $station->rel,
                    ]);
                } catch (\Exception $e) {}
            }
        }

        # Step 3
        self::connectMck(219, 221, 220);
        self::connectMck(220, 222, 221);
        self::connectMck(221, 223, 222);
        self::connectMck(222, 224, 223);
        self::connectMck(223, 225, 224);
        self::connectMck(224, 226, 225);
        self::connectMck(225, 227, 226);
        self::connectMck(226, 228, 227);
        self::connectMck(227, 229, 228);
        self::connectMck(228, 230, 229);
        self::connectMck(229, 231, 230);
        self::connectMck(230, 232, 231);
        self::connectMck(231, 233, 232);
        self::connectMck(232, 234, 233);
        self::connectMck(233, 235, 234);
        self::connectMck(234, 219, 235);
        self::connectMck(235, 220, 219);
    }

    private static function rule($has, $new) {
        $conditions = [];
        foreach($has as $station_id) {
            $conditions[] = "EXISTS (SELECT 1 FROM tutor_departures WHERE tutor_departures.station_id={$station_id} AND tutors.id = tutor_departures.tutor_id)";
        }
        $tutor_ids = DB::table('tutors')->whereRaw(implode(' AND ', $conditions))->pluck('id');
        foreach($tutor_ids as $tutor_id) {
            foreach($new as $station_id) {
                try {
                    DB::table('tutor_departures')->insert(compact('tutor_id', 'station_id'));
                } catch (\Exception $e) {}
            }
        }
    }

    private static function connectMck($a, $b, $c)
    {
        $conditions = [];
        $conditions[] = "EXISTS (SELECT 1 FROM tutor_departures WHERE tutor_departures.station_id={$a} AND tutors.id = tutor_departures.tutor_id)";
        $conditions[] = "EXISTS (SELECT 1 FROM tutor_departures WHERE tutor_departures.station_id={$b} AND tutors.id = tutor_departures.tutor_id)";
        $tutor_ids = DB::table('tutors')->whereRaw(implode(' AND ', $conditions))->pluck('id');
        foreach($tutor_ids as $tutor_id) {
            try {
                DB::table('tutor_departures')->insert([
                    'tutor_id' => $tutor_id,
                    'station_id' => $c
                ]);
            } catch (\Exception $e) {}
        }
    }
}
