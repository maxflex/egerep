<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class SummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = intval($request->page) ? $request->page-1 : 0;
        $date = new \DateTime();
        $start_date = $date->sub(new \DateInterval("P{$page}M"))->format('Y-m-d');

        $date->sub(new \DateInterval('P1M'));
        $end_date = $date->format('Y-m-d');

        /**
         * @notice as time : такой алиас, а не date, потому что в таблице аттачментс уже есть поле date,
         *                   и этот приводит к ошибке "ambiguous field"
         */
        $requests = DB::table('requests')
                        ->select(DB::raw('COUNT(*) as cnt, DATE(created_at) as time'))
                        ->whereRaw("DATE(created_at) > '{$end_date}'")
                        ->whereRaw("DATE(created_at) <= '{$start_date}'")
                        ->groupBy('time')->get();

        $attachments = DB::table('attachments')
                            ->select(DB::raw('COUNT(*) as cnt, DATE(created_at) as time'))
                            ->whereRaw("date(created_at) > '{$end_date}'")
                            ->whereRaw("DATE(created_at) <= '{$start_date}'")
                            ->groupBy('time')->get();


        $clients = DB::table('clients')
                        ->select(DB::raw('count(*) as cnt, DATE(created_at) as time'))
                        ->whereRaw("DATE(created_at) > '{$end_date}'")
                        ->whereRaw("DATE(created_at) <= '{$start_date}'")
                        ->groupBy('time')->get();


        /**
         * @notice (new \DateTime($start_date))->add($interval) :  add($interval) чтобы последняя дата тоже вошла.
         **/
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod(new \DateTime($end_date), $interval, (new \DateTime($start_date))->add($interval), \DatePeriod::EXCLUDE_START_DATE);

        $return = [];
        foreach ($period as $dt) {
            $return[$dt->format("Y-m-d")] = []; // для лоопа в ангуляре все даты.
            foreach (['requests', 'attachments' /*, 'clients' */] as $elems) {
                foreach ($$elems as $elem) {
                    if ($elem->time == $dt->format("Y-m-d")) {
                        $return[$dt->format("Y-m-d")][$elems] = $elem;
                        break;
                    }
                }
            }
        }

        /**
         * сортируем по дате
         */
        $return = array_reverse($return);
        echo json_encode($return);
    }
}
