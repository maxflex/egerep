<?php

namespace App\Console\Commands\Once;

use Illuminate\Console\Command;

class AccountComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:account_comments';

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
        $accounts = \DB::table('accounts')->select('id', 'comment', 'user_id', 'created_at')
            ->where('comment', '<>', '')->get();

        foreach($accounts as $account) {
            \DB::table('comments')->insert([
                'entity_type' => 'account',
                'entity_id' => $account->id,
                'user_id' => $account->user_id,
                'comment' => $account->comment,
                'created_at' => $account->created_at,
                'updated_at' => $account->created_at
            ]);
        }
    }
}
