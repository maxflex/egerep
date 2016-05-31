<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class UpdateClientGrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:update_client_grade';

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
        $clients = DB::table('clients')->where('grade', 0)->pluck('id');

        foreach ($clients as $client_id) {
            $attachments = DB::table('attachments')->where('client_id', $client_id)
                                                    ->where('date', '<', '2015-03-01');
            if ($attachments->exists()) {
                \App\Models\Client::where('id', $client_id)->update([
                    'grade' => 12
                ]);
            }
        }
    }
}
