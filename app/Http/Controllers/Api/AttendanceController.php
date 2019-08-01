<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Models\Service\Fingerscan;
use Cache;

class AttendanceController extends Controller
{
    public function get(Request $request)
    {
        $year_months = DB::table('attendance')->select(DB::raw("DATE_FORMAT(`date`,'%Y-%m') as `year_month`"))->orderBy('date', 'asc')->groupBy(DB::raw("DATE_FORMAT(`date`,'%Y-%m')"))->pluck('year_month');
        $data = [];

        foreach($year_months as $year_month) {
            $data[$year_month] = DB::table('attendance')->whereRaw("DATE_FORMAT(`date`,'%Y-%m') = '{$year_month}'")->get();
        }

        // добавить текущий день в LIVE
        $today_data = Fingerscan::get(now(true));
        $data[$year_month] = array_merge($data[$year_month], $today_data);

        $return = [];


        foreach($data as $year_month => $year_month_data) {
            foreach($year_month_data as $d) {
                // формируем LABEL – разница между 10:00 и временем прихода в формате H:MM
                $datetime1 = new \DateTime(toDate($d->date) . ' 10:00:00');
                $datetime2 = new \DateTime($d->date);
                $interval = $datetime1->diff($datetime2);
                $label = '';
                if (! $interval->invert) {
                    if ($interval->h) {
                        $label .= $interval->h . ':';
                    }
                    if ($interval->i) {
                        $label .= ($interval->i < 10 && $interval->h) ? ('0' . $interval->i) : $interval->i;
                    } else if ($interval->h) {
                        $label .= '00';
                    }
                }
                $login = Cache::remember("egerep:logins:{$d->user_id}", 1, function() use ($d) {
                    return dbEgecrm2('admins')->whereId($d->user_id)->value('nickname');
                });
                if ($login !== null) {
                    $return[$year_month][$login][date('j', strtotime($d->date))] = $label;
                }
            }
        }

        return $return;
    }

    private static function generateDates($year, $month)
    {
        if (strlen($month) == 1) {
            $month = '0' . $month;
        }

        return (object)[
            'start' => implode('-', [$year, $month, '01']),
            'end'   => implode('-', [$year, $month, '31']),
        ];
    }
}
