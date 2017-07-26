<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('logs')->whereDate('created_at', '=', fromDotDate($request->date))->where('user_id', $request->user_id);
        $mango_query = DB::table('mango')->where(DB::raw('DATE(FROM_UNIXTIME(start))', $request->date));

        $data = cloneQuery($query)->select('created_at')->orderBy('created_at', 'asc')->pluck('created_at');

        if (! count($data)) {
            return -1;
        }

        $return['first_action_time'] = self::timeFormat($data[0]);
        $return['last_action_time'] = self::timeFormat($data[count($data) - 1]);

        // подсчитываем разницу во времени между действиями
        $pauses = [];

        if (count($data) > 2) {
            $diffs = [];

            foreach(range(0, count($data) - 2) as $i) {
                $d1 = new \DateTime($data[$i]);
                $d2 = new \DateTime($data[$i + 1]);
                $interval = $d1->diff($d2);
                $diffs[$i] = ($interval->h * 60) + $interval->i; // разница в минутах
            }

            asort($diffs);

            foreach(array_slice(array_reverse($diffs, true), 0, 5, true) as $i => $diff) {
                $pauses[] = [
                    'start' => self::timeFormat($data[$i]),
                    'end'   => self::timeFormat($data[$i + 1]),
                    'diff'  => $diff
                ];
            }
        }

        $return['pauses'] = $pauses;
        $return['database_operations'] = cloneQuery($query)->where('row_id', '>', 0)->count();
        $return['url_views'] = cloneQuery($query)->where('type', 'url')->count();
        $return['outgoing_calls_successful'] = cloneQuery($mango_query)->where('from_extension', $request->user_id)->where('answer', '>', 0)->count();
        $return['outgoing_calls_failed'] = cloneQuery($mango_query)->where('from_extension', $request->user_id)->where('answer', 0)->count();
        $return['incoming_calls'] = cloneQuery($mango_query)->where('to_extension', $request->user_id)->count();
        $return['calls_duration'] = round(cloneQuery($mango_query)->where(DB::raw("(from_extension={$request->user_id} or to_extension={$request->user_id})"))->where('answer', '>', 0)->sum(DB::raw('finish - answer')) / 60);

        return $return;
    }

    private static function timeFormat($date)
    {
        return date('H:i', strtotime($date));
    }
}
