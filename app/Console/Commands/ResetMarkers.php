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
    protected $signature = 'markers:reset {entity=tutor}';

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
        $entity = ucfirst($this->argument('entity'));
        $this->info($entity);
        \DB::statement("
            DELETE metros FROM metros
            JOIN markers ON markers.id = metros.marker_id
            WHERE markers.markerable_type = 'App\\\Models\\\\$entity'
        ");
        $this->info('Getting markers...');
        $markers = Marker::where('markerable_type', "App\Models\\$entity")->get();

        $this->info(count($markers) . " markers found. Creating metros...");
        foreach($markers as $marker) {
            $marker->createMetros();
        }
    }
}
