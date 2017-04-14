<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class TransferAccountPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:account_payments';

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
        DB::table('account_payments')->truncate();

        $accounts = DB::table('accounts')->get();
        $bar = $this->output->createProgressBar(count($accounts));

        foreach($accounts as $account) {
            DB::table('account_payments')->insert([
                'account_id' => $account->id,
                'sum' => $account->received,
                'method' => $account->payment_method,
                'date' => $account->date_end,
                'user_id' => $account->user_id,
                'created_at' => $account->created_at,
                'updated_at' => $account->updated_at,
            ]);
            $bar->advance();
        }
        $bar->finish();
    }
}
