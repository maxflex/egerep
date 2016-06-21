<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Request;
use App\Models\Comment;

class UpdateRequestUserId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:request_user_id';

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
        $this->info('Getting requests...');

        $requests = \DB::table('requests')->get();

        $bar = $this->output->createProgressBar(count($requests));

        foreach($requests as $request) {
            $user_ids = \DB::table('comments')->where('entity_type', 'request')->where('entity_id', $request->id)->pluck('user_id');
            $user_ids[] = $request->user_id_created;

            foreach($user_ids as $user_id) {
                $percentage = (array_count_values($user_ids)[$user_id] / count($user_ids)) * 100;
                if ($percentage > 50) {
                    \DB::table('requests')->where('id', $request->id)->update(compact('user_id'));
                    //$request->user_id = $user_ids[$index];
                    break;
                }
            }

            $bar->advance();
        }

        $bar->finish();
    }
}
