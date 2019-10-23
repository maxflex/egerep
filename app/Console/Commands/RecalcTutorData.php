<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Review;
use App\Models\Tutor;
use App\Models\Service\Youtube;

class RecalcTutorData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recalc:tutor_data {tutor_id?}';

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
        $tutors_query = DB::table('tutors')->whereRaw("(public_desc <> '' OR (description <> '' AND in_egecentr=2))");
        if ($updated_tutor_id = $this->argument('tutor_id')) {
            DB::table('tutor_data')->where('tutor_id', $updated_tutor_id)->delete();
            $tutors_query->whereId($updated_tutor_id);
        } else {
            DB::table('tutor_data')->truncate();
        }
        $tutor_ids = $tutors_query->pluck('id');
        $bar = $this->output->createProgressBar(count($tutor_ids));

        foreach($tutor_ids as $tutor_id) {
            $data = DB::table('tutors')->whereId($tutor_id)->select(
                DB::raw('(select group_concat(station_id) FROM tutor_departures td WHERE td.tutor_id = tutors.id) as svg_map'),
                DB::raw('(SELECT COUNT(*) FROM attachments WHERE attachments.tutor_id = tutors.id) as clients_count')
            )->first();
            DB::table('tutor_data')->insert([
                'tutor_id'      => $tutor_id,
                'clients_count' => $data->clients_count,
                'svg_map' => $data->svg_map,
                'reviews_count' => DB::table('reviews')
                                    ->join('attachments', 'attachments.id', '=', 'attachment_id')
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
        $tutor = DB::table('tutors')->whereId($tutor_id)->select('lk', 'tb', 'js')->first();

        $data = DB::table('attachments')->where('tutor_id', $tutor_id)
                    ->leftJoin(DB::raw('(SELECT attachment_id, score FROM reviews WHERE score BETWEEN 1 AND 10) as r'), 'r.attachment_id', '=', 'attachments.id')
                    ->select(DB::raw("r.score, (SELECT COUNT(*) FROM account_datas WHERE account_datas.attachment_id = attachments.id) as lesson_count"))
                    ->get();

        // общий вес
        $total_weight = 0;

        // наша оценка
        switch($tutor->js) {
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
                $js = $tutor->js;
            }
        }

        $our_score = ($tutor->lk + $tutor->tb + $js) / 3;

        $total_weight = 0;
        $total_score = 0;

        // подсчет числителя
        foreach($data as $d) {
            if ($d->lesson_count == 0) {
                $score = 2;
                $weight = 0.1;
            } else
            if ($d->lesson_count == 1) {
                $score = 3;
                $weight = 0.4;
            } else
            if ($d->lesson_count == 2) {
                $score = 4;
                $weight = 0.5;
            } else
            if ($d->lesson_count >= 3 && $d->lesson_count <= 4) {
                $score = 6;
                $weight = 0.6;
            } else
            if ($d->lesson_count >= 5 && $d->lesson_count <= 8) {
                $score = 7;
                $weight = 0.7;
            } else
            if ($d->lesson_count >= 9 && $d->lesson_count <= 15) {
                $score = 8;
                $weight = 0.75;
            } else {
                $score = 9;
                $weight = 0.8;
            }

            // отзыв с оценкой присутствует
            if ($d->score) {
                $score = $d->score + ($score * $weight);
                $weight += 1;
            } else {
                $score = $score * $weight;
            }

            $total_weight += $weight;
            $total_score += $score;
        }

        $avg = (4 * $our_score * 0.8 + $total_score) / (4 + $total_weight);

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
