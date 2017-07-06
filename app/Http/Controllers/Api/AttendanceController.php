<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Models\Service\Fingerscan;

class AttendanceController extends Controller
{
    public function get(Request $request)
    {
        $month = $request->month;
        // если выбрали будущий месяц, то показываем статистику за предыдущий год этого месяца
        $year = $month > date('n') ? date('Y') - 1 : date('Y');

        $date = self::generateDates($year, $month);


        $data = DB::table('attendance')->whereRaw("(date >= '{$date->start}' AND date <= '{$date->end}')")->get();

        // если текущий месяц, к датам надо добавить текущий день в LIVE
        if (date('n') == $month) {
            $today_data = Fingerscan::get(now(true));
            $data = array_merge($data, $today_data);
        }

        $return = [];

        foreach($data as $d) {
            $return[$d->user_id][date('j', strtotime($d->date))] = date('H:i', strtotime($d->date));
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
