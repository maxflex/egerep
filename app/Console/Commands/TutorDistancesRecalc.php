<?php

namespace App\Console\Commands;

use App\Models\Station;
use App\Models\Tutor;
use Illuminate\Console\Command;

class TutorDistancesRecalc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutor_distances:recalc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalc tutor distances for ege-web';

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
        \DB::table('tutor_distances')->truncate();


        $tutor_ids = \DB::table('tutors')->where('public_desc', '!=', '')->pluck('id');
        $station_ids = Station::pluck('id');

        $bar = $this->output->createProgressBar(count($tutor_ids));

        foreach ($tutor_ids as $tutor_id) {
            foreach ($station_ids as $station_id) {
                $distances = static::getDistances($tutor_id, $station_id);
                \DB::table('tutor_distances')->insert([
                    'tutor_id'       => $tutor_id,
                    'station_id'     => $station_id,
                    'metro_minutes'  => $distances->metro_minutes,
                    'marker_minutes' => $distances->marker_minutes,
                ]);
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info('Distances recalculated');
    }

    private static function getDistances($tutor_id, $station_id) {
        return Tutor::whereId($tutor_id)->select(\DB::raw(
            "
            (select min(distance)
                from distances
                where
                    exists (select 1 from tutor_departures td where td.tutor_id = tutors.id and `from` = td.station_id) and `to` = {$station_id}
            ) as metro_minutes,
            (select min(d.distance + m.minutes)
                 from markers mr
                 join metros m on m.marker_id = mr.id
                 join distances d on d.from = m.station_id and d.to = {$station_id}
                 where
                    mr.markerable_id = tutors.id and mr.markerable_type = 'App\\\\Models\\\\Tutor' and mr.type='green'
            ) as marker_minutes
        "))->first();
    }
}
