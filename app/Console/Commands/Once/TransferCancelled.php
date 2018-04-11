<?php

namespace App\Console\Commands\Once;

use Illuminate\Console\Command;

class TransferCancelled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:transfer_cancelled';

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
        $data = \DB::connection('egecrm')->select("
            select
                gs.id as gs_id,
                vj.id as vj_id,
                gs.cancelled as gs_cancelled,
                vj.cancelled as vj_cancelled,
                gs.time as gs_time,
                vj.lesson_time as vj_time,
                gs.date as gs_date,
                vj.lesson_date as vj_date,
                gs.id_group as gs_id_group,
                vj.id_group as vj_id_group
            from (select * from `--group_schedule` where cancelled=1) gs
            left join visit_journal vj on vj.id_group=gs.id_group and vj.lesson_time = gs.time and vj.lesson_date = gs.date
        ");

        $bar = $this->output->createProgressBar(count($data));

        foreach($data as $d) {
            if (! $d->vj_id) {
                dbEgecrm('visit_journal')->insert([
                    'lesson_date' => $d->gs_date,
                    'lesson_time' => $d->gs_time,
                    'id_group' => $d->gs_id_group,
                    'cancelled' => 1
                ]);
            }
            $bar->advance();
        }

        $bar->finish();
    }
}
