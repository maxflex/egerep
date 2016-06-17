<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TransferActiveClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:active_clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer active clients & attachments';

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
        $this->info('Getting history...');

        $history = \DB::connection('egerep')->table('prognoz_history')->get();

        $bar = $this->output->createProgressBar(count($history));

        foreach ($history as $h) {
            $clients_count = explode(',', $h->clients_count);
            $statuses = [];
            foreach ($clients_count as $clients_count_string) {
                list($status, $count)= explode(":", $clients_count_string);
                $statuses[$status] = $count;
            }

            Summary::where('date', $h->date)->update([
                'new_clients'        => $statuses[1],
                'active_attachments' => ($statuses[2] + $statuses[3]),
            ]);

            $bar->advance();
        }

        $bar->finish();
    }
}
