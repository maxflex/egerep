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

            DB::table('tutors')->where('id', $tutor_id)->update(['margin' => round($total_commission / count($hidden_client_ids))]);
            $bar->advance();
        }

        $bar->finish();
    }
}
