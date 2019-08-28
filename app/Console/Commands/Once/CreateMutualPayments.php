<?php

namespace App\Console\Commands\Once;

use Illuminate\Console\Command;
use DB;
use App\Models\User;

class CreateMutualPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:create-mutual-payments';

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
        $payments = dbEgecrm('payments')->where('id_status', 6)->get();

        $bar = $this->output->createProgressBar(count($payments));

        foreach($payments as $payment) {
            DB::table('account_payments')->insert([
                'account_id' => $payment->account_id,
                'sum' => $payment->sum,
                'date' => $payment->date,
                'user_id' => User::where('old_id', $payment->id_user)->value('id'),
                'method' => 4,
                'confirmed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $bar->advance();
        }
        $bar->finish();
    }
}
