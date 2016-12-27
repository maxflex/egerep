<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TransferTutorDeparture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:tutor_departure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'transfer data from svg_map field to tutors_departure table';

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
        \DB::table('tutor_departures')->truncate();

        $tutor_query = \DB::table('tutors')->select(['id', '_svg_map']);

        $bar = $this->output->createProgressBar($tutor_query->count());
        foreach ($tutor_query->get() as $tutor) {
            if ($tutor->_svg_map) {
                $data = [];
                $station_ids = explode(',', $tutor->_svg_map);

                foreach (array_unique($station_ids) as $station_id) {
                    $data[] = ['tutor_id' => $tutor->id, 'station_id' => $station_id];
                }

                \DB::table('tutor_departures')->insert($data);
            }

            $bar->advance();
        }
        $bar->finish();
    }
}
