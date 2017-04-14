<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class OnceMutualPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:mutual_payments';

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
        $payments = dbEgecrm('payments')->where('entity_type', 'TEACHER')->where('id_status', 6)->get();

        $bar = $this->output->createProgressBar(count($payments));
        $updated = 0;
        foreach ($payments as $payment) {
            $query = DB::table('accounts')->where('date_end', fromDotDate($payment->date))->where('tutor_id', $payment->entity_id);
            if ($query->count() == 1) {
                $updated++;
                dbEgecrm('payments')->whereId($payment->id)->update([
                    'account_id' => $query->value('id')
                ]);
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info("\n\n{$updated} rows updated\n");
    }
}
