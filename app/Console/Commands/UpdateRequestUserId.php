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
            $user_ids = Comment::where('entity_type', 'request')->where('entity_id', $request->id)->pluck('user_id')->all();
            $user_ids[] = $request->user_id_created;

            foreach(static::_getPercentage($user_ids) as $index => $percentage) {
                if ($percentage > 50) {
                    \DB::table('requests')->where('id', $request->id)->update([
                        'user_id' => $user_ids[$index]
                    ]);
                    //$request->user_id = $user_ids[$index];
                    break;
                }
            }

            $bar->advance();
        }

        $bar->finish();
    }

    private static function _getPercentage($share)
    {
        $total = array_sum($share);

        $share = array_map(function($hits) use ($total) {
           return round($hits / $total * 100, 1) . '%';
        }, $share);

        return $share;
    }

}
