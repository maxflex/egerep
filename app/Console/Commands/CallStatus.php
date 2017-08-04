<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class CallStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set call statuses';

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
        dbEgecrm('call_statuses')->truncate();

        $this->line("Incoming without answer...");

        // входящие без ответа
        $ids = dbEgecrm('mango')->where('answer', 0)->where('from_extension', 0)->pluck('id');

        $this->info(count($ids));

        foreach($ids as $id) {
            dbEgecrm('call_statuses')->insert([
                'id' => $id,
                'status' => 1
            ]);
        }

        // номер соответствует договору
        // dbEgecrm('mango as m')->
    }
}
