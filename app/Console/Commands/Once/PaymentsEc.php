<?php

namespace App\Console\Commands\Once;

use Illuminate\Console\Command;
use DB;

class PaymentsEc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:payments_ec';

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
        $google_ids = file('file_in.txt', FILE_IGNORE_NEW_LINES);
        $bar = $this->output->createProgressBar(count($google_ids));
        foreach($google_ids as $id_google) {
            if (dbEgecrm('requests')->where('id_google', $id_google)->exists()) {
                $sum = DB::connection('egecrm')->select("SELECT sum(if(id_type=1, `sum`, `sum` * -1)) as `sum` from payments p
                    join
                    (
                        select id_student from requests where id_google='{$id_google}'
                        group by id_student
                    ) x on x.id_student = p.entity_id
                    where p.entity_type='STUDENT' AND p.`year`=2017
                ")[0]->sum;
                $sum = $sum ?: '0';
            } else {
                $sum = 'no match';
            }
            fwrite($f, "{$id_google}\t{$sum}\n");
            $bar->advance();
        }
    }
}
