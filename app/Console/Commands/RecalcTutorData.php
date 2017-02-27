<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class RecalcTutorData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recalc:tutor_data';

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
        DB::table('tutor_data')->truncate();
        $tutor_ids = DB::table('tutors')->where('public_desc', '!=', '')->pluck('id');
        $bar = $this->output->createProgressBar(count($tutor_ids));

        foreach($tutor_ids as $tutor_id) {
            $data = DB::table('tutors')->whereId($tutor_id)->select(
                DB::raw('(select group_concat(station_id) FROM tutor_departures td WHERE td.tutor_id = tutors.id) as svg_map'),
                DB::raw('(SELECT COUNT(*) FROM attachments WHERE attachments.tutor_id = tutors.id) as clients_count'),
                DB::raw('(SELECT MIN(date) FROM attachments WHERE attachments.tutor_id = tutors.id) as first_attachment_date')
            )->first();
            DB::table('tutor_data')->insert([
                'tutor_id'      => $tutor_id,
                'clients_count' => $data->clients_count,
                'first_attachment_date' => $data->first_attachment_date,
                'svg_map' => $data->svg_map,
                // 'reviews_count' => DB::table('reviews')
                //                     ->join('attachments', 'attachments.id', '=', 'attachment_id')
                //                     ->join('archives', 'archives.attachment_id', '=', 'attachments.id')
                //                     ->where('tutor_id', $tutor_id)
                //                     ->where('reviews.state', 'published')
                //                     ->whereBetween('score', [1, 10])
            ]);
            $bar->advance();
        }
        $bar->finish();
    }
}
