<?php

namespace App\Console\Commands\Once;

use Illuminate\Console\Command;
use DB;

class PaymentsEc2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:payments_ec2';

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
        $f = fopen('file_out.txt', 'w');
        $google_ids = file('file_in.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $bar = $this->output->createProgressBar(count($google_ids));
        foreach($google_ids as $id_google) {
            $student_ids = dbEgecrm('requests')->where('id_google', $id_google)->groupBy('id_student')->pluck('id_student');
            if (count($student_ids)) {
                $student_ids = implode(',', $student_ids);
                $sum = DB::connection('egecrm')->select("SELECT sum(if(id_type=1, `sum`, `sum` * -1)) as `sum` from payments
                    where entity_type='STUDENT'
                        AND entity_id IN ({$student_ids})
                        AND `date` BETWEEN '2018-07-01' AND '2018-10-30'
                ")[0]->sum;
                fwrite($f, "{$id_google}\t{$sum}\n");
            }
            $bar->advance();
        }
    }
}
