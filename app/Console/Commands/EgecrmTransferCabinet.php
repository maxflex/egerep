<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class EgecrmTransferCabinet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'egecrm:cabinets';

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
        $data = DB::connection('egecrm')->table('group_time')->get();

        $bar = $this->output->createProgressBar(count($data));

        foreach($data as $d) {
            $time = \DB::connection('egecrm')->table('time')->whereId($d->id_time)->first();
            $query = \DB::connection('egecrm')->table('group_schedule')->whereRaw("DAYOFWEEK(date) = {$time->day} AND time='{$time->time}:00' AND cabinet > 0 AND id_group=" . $d->id_group);
            if ($query->exists()) {
                \DB::connection('egecrm')->table('group_time')->where('id_group', $d->id_group)->where('id_time', $d->id_time)->update([
                    'id_cabinet' => $query->value('cabinet')
                ]);
            }
        }
    }
}
