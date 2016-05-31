<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Settings;

class RecalcDebt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:recalc_debt';

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
        $this->line('Starting...');

        $accounts = \App\Models\Account::all();

        $bar = $this->output->createProgressBar($accounts->count());

        foreach ($accounts as $account) {
            $account->recalcDebt($account->date_start, $account->date_end);
            $bar->advance();
        }

        Settings::set('debt_updated', now());

        $bar->finish();
    }
}
