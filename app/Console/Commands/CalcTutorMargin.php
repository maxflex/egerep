<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tutor;
use DB;

class CalcTutorMargin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calc:tutor_margin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate tutor margin';

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
        // выбираем только тех репетиторов, у которых есть хотя бы минимум 4 скрытых клиента
        $tutor_ids = DB::table('tutors')->whereRaw("(SELECT COUNT(*) FROM attachments WHERE tutor_id = tutors.id AND hide=1) >= 4")->pluck('id');

        $bar = $this->output->createProgressBar(count($tutor_ids));

        foreach($tutor_ids as $tutor_id) {
            $hidden_client_ids = DB::table('attachments')->where('hide', 1)->where('tutor_id', $tutor_id)->pluck('client_id');

            $a = DB::table('account_datas')->where('tutor_id', $tutor_id)->where('commission', '>', 0)->whereIn('client_id', $hidden_client_ids)->sum('commission');
            $b = DB::table('account_datas')->where('tutor_id', $tutor_id)->where('commission', 0)->whereIn('client_id', $hidden_client_ids)->sum('sum');
            $total_commission = ($b * 0.25) + $a;

            $m = round($total_commission / count($hidden_client_ids));
            
            $margin = 0;
            if ($m >= 3000 && $m < 5000) {
                $margin = 1;
            } else
            if ($m >= 5000 && $m < 7000) {
                $margin = 2;
            } else
            if ($m >= 7000 && $m < 10000) {
                $margin = 3;
            } else
            if ($m >= 10000 && $m < 13000) {
                $margin = 4;
            } else
            if ($m >= 13000) {
                $margin = 5;
            }

            DB::table('tutors')->where('id', $tutor_id)->update(compact('margin'));
            $bar->advance();
        }

        $bar->finish();
    }
}
