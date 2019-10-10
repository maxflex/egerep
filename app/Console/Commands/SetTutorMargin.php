<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Tutor;
use App\Models\Attachment;

class SetTutorMargin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:margin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Установить уровень маржинальности';

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
        DB::table('tutors')->update(['margin' => null]);

        $tutor_ids = Tutor::has('attachments')->pluck('id');

        $bar = $this->output->createProgressBar(count($tutor_ids));

// 		$tutor_ids = [1231];


        foreach($tutor_ids as $tutor_id) {
            //  с 15 июля 2016 года по 15 июля 2018 года
            $attachmentIds = Attachment::has('archive')
                ->where('tutor_id', $tutor_id)
                ->where('date', '<', '2019-06-01')
                ->orderBy('id', 'desc')
                ->take(40)
                ->pluck('id');


            // если всего у репетитора стыковок 0-2, то параметр вообще не считаем
            $attachment_count = $attachmentIds->count();
            if ($attachment_count <= 2) {
                $bar->advance();
                continue; // = margin=null
            }

            $commission = DB::table('account_datas')->whereIn('attachment_id', $attachmentIds)
                ->select(DB::raw('round(sum(if(commission > 0, commission, 0.25 * sum))) as `sum`'))->value('sum');

            $avg_commission = $commission / $attachment_count;

/*
            $this->info("att_ids: " . implode(', ', $attachmentIds->all()));
            $this->info("commission: " . $commission);
            $this->info("avg_commission: " . $avg_commission);
            $this->info("attachment_count: " . $attachment_count);
*/


            $margin = ceil($avg_commission / 2000);

            if ($margin == 0) {
                $margin = 1;
            }
            DB::table('tutors')->whereId($tutor_id)->update(compact('margin'));
            $bar->advance();
        }
        $bar->finish();
    }
}
