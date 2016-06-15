<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Request;

class CheckFinishedRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:finished_requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'All finished requests have at least one attachment & deny requests have no attachments';

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
        $this->line('Getting finished requests');

        $requests = Request::where('state', 'finished')->get();

        $bar = $this->output->createProgressBar($requests->count());

		$request_ids = [];

        foreach ($requests as $request) {
            $bar->advance();
            if ($request->lists) {
                foreach ($request->lists as $list) {
                    if ($list->attachments()->count()) {
                        continue 2;
                    }
                }
            }
            $request_ids[] = $request->id;
//             $this->info($request->id);
        }
        $bar->finish();

        foreach ($request_ids as $id) {
	        $this->info($id);
        }
    }
}
