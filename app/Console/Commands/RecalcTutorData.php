<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Review;
use App\Models\Tutor;

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
                'reviews_count' => DB::table('reviews')
                                    ->join('attachments', 'attachments.id', '=', 'attachment_id')
                                    ->join('archives', 'archives.attachment_id', '=', 'attachments.id')
                                    ->where('tutor_id', $tutor_id)
                                    ->where('reviews.state', 'published')
                                    ->whereBetween('score', [1, 10])->count(),
                'review_avg' => static::_getReviewAvg($tutor_id),
                'photo_exists' => static::_photoExists($tutor_id),
            ]);
            $bar->advance();
        }
        $bar->finish();
    }

    private static function _getReviewAvg($tutor_id)
    {
        $data = DB::table('tutors')->whereId($tutor_id)->select('lk', 'tb', 'js')->first();
        $query = Review::join('attachments', 'attachments.id', '=', 'attachment_id')->where('tutor_id', $tutor_id)->whereBetween('score', [1, 10]);
        $sum = $query->newQuery()->sum('reviews.score');
        $count = $query->newQuery()->count();
        switch($data->js) {
            case 6:
            case 10: {
                $js = 8;
                break;
            }
            case 8: {
                $js = 10;
                break;
            }
            case 7: {
                $js = 9;
                break;
            }
            default: {
                $js = $data->js;
            }
        }
        $avg = (4 * (($data->lk + $data->tb + $js) / 3) + $sum)/(4 + $count);
        return $avg;
    }

    private static function _photoExists($tutor_id)
    {
        $photo_extension = DB::table('tutors')->whereId($tutor_id)->value('photo_extension');
        if ($photo_extension) {
            $filename = public_path() . Tutor::UPLOAD_DIR . $tutor_id . '.' . $photo_extension;
            return file_exists($filename);
        }
        return false;
    }
}
