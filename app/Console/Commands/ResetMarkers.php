<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Metro;
use App\Models\Marker;

class ResetMarkers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'markers:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all tutor markers';

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
        \DB::statement("
            DELETE metros FROM metros
            JOIN markers ON markers.id = metros.marker_id
            WHERE markers.markerable_type = 'App\\\Models\\\Tutor'
        ");

        $this->info('Getting markers...');
        $markers = Marker::where('markerable_type', 'App\Models\Tutor')->get();

        $this->info('Creating metros...');
        foreach($markers as $marker) {
            $marker->createMetros();
        }
    }
}
