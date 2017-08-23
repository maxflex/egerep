<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class DeleteMarginIntermediate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:margin';

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
        // select a.tutor_id, count(*) as cnt from attachments a
        // join archives ar on (ar.attachment_id = a.id and ar.`date` <= '2017-04-30' and (ar.total_lessons_missing is null or ar.total_lessons_missing=0))
        // group by a.tutor_id

        $tutor_ids = DB::table('attachments as a')->join('archives as ar', function($join) {
            return $join->on('ar.attachment_id', '=', 'a.id')->where('ar.state', 'impossible');
        })->whereRaw('(ar.total_lessons_missing is null or ar.total_lessons_missing=0)')->groupBy('a.tutor_id')->pluck('tutor_id');

        $data = "";
        foreach($tutor_ids as $tutor_id) {
            // получить последние 50 стыковок преподавателя
            $attachment_ids = DB::table('attachments')->where('tutor_id', $tutor_id)->orderBy('date', 'desc')->take(50)->pluck('id');
            $tutor = DB::table('tutors')->whereId($tutor_id)->select(DB::raw("CONCAT(last_name, ' ', first_name, ' ', middle_name) as name"))->value('name');
            $count = count($attachment_ids);
            $commission = DB::table('account_datas')->whereIn('attachment_id', $attachment_ids)
                ->select(DB::raw('round(sum(if(commission > 0, commission, 0.25 * sum))) as `sum`'))->value('sum');
            $data .= implode("\t", [$tutor, $count, $commission, $count > 0 ? $commission/$count : null]) . "\n";
        }
        \Storage::put('table.txt', $data);
    }
}
