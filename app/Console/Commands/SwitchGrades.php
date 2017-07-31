<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class SwitchGrades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'switch:grades';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch grades (every summer)';

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
        /**
         * @todo: не работает increment
         */

        // 11 и экстернат в студенты
        dbEgecrm('students')->whereIn('grade', [11, 14])->update(['grade' => 12]);
        // с 1 по 10 +1
        dbEgecrm('students')->whereBetween('grade', [1, 10])->increment('grade');

        // 11 и экстернат в студенты
        DB::table('clients')->whereIn('grade', [11, 14])->update(['grade' => 12]);
        // с 1 по 10 +1
        DB::table('clients')->whereBetween('grade', [1, 10])->increment('grade');
    }
}
