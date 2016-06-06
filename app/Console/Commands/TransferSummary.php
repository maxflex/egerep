<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service\Summary;

class TransferSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer forecast history from old CRM';

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

        $history = DB::connection('egerep')->table('prognoz_history')->get();

        Summary::truncate();

        $bar = $this->output->createProgressBar(count($history));

        foreach ($history as $h) {
            Summary::create([
                'date'      => $h->date,
                'forecast'  => ($h->actual + $h->virtual) / 4,
                'debt'      => $h->debet,
            ]);
            $bar->advance();
        }

        $bar->finish();
    }
}
