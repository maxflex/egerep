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
        $return = [];

        switch ($request->filter) {
            case 'week':
                $return = $this->getByWeek($request);
                break;
            case 'month':
                $return = $this->getByMonth($request);
                break;
            case 'year':
                $return = $this->getByYear($request);
                break;
            case 'day':
            default:
                $return = $this->getByDay($request);
        }
        echo json_encode($return);
    }

    private function getByDay($request)
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


        // $clients = DB::table('clients')
        //     ->select(DB::raw('count(*) as cnt, DATE(created_at) as time'))
        //     ->whereRaw("DATE(created_at) > '{$end_date}'")
        //     ->whereRaw("DATE(created_at) <= '{$start_date}'")
        //     ->groupBy('time')->get();


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
        return array_reverse($return);
    }

    private function getByWeek($request)
    {
        $page = intval($request->page) ? $request->page-1 : 0;
        $date = new \DateTime('sunday');
        $skip_days = $page*30*7;

        $start_date = $date->sub(new \DateInterval("P{$skip_days}D"))->format('Y-m-d');
        $end_date   = $date->sub(new \DateInterval('P210D'))->format('Y-m-d'); // 210 = 7 days * 30 weeks

        $requests = DB::table('requests')
            ->select(DB::raw('COUNT(*) as cnt, STR_TO_DATE(CONCAT(YEARWEEK(created_at, 1) + 1, \'Sunday\'), \'%X%V %W\') as time'))
            ->whereRaw("DATE(created_at) >= '{$end_date}'")
            ->whereRaw("DATE(created_at) <= '{$start_date}'")
            ->groupBy(DB::raw('YEARWEEK(created_at, 1)'))
            ->get();

        $attachments = DB::table('attachments')
            ->select(DB::raw('COUNT(*) as cnt, STR_TO_DATE(CONCAT(YEARWEEK(created_at, 1) + 1, \'Sunday\'), \'%X%V %W\') as time'))
            ->whereRaw("date(created_at) >= '{$end_date}'")
            ->whereRaw("DATE(created_at) <= '{$start_date}'")
            ->groupBy(DB::raw('YEARWEEK(created_at, 1)'))
            ->get();

        $return = [];
        $start = new \DateTime($start_date);
        $end   = new \DateTime($end_date);

        while ($end <= $start) {
            $today = false;

            if ($end > new \DateTime()) {
                $today = (new \DateTime())->format('Y-m-d');
            }

            $return[$today ? $today : $end->format("Y-m-d")] = [];

            foreach (['requests', 'attachments'] as $elems) {
                foreach ($$elems as $elem) {
                    if ($elem->time == $end->format("Y-m-d")) {
                        $return[$today ? $today : $end->format("Y-m-d")][$elems] = $elem;
                        break;
                    }
                }
            }

            $end->modify("+1 week");
        }

        /**
         * сортируем по дате
         */
        return array_reverse($return);
    }

    private function getByMonth($request)
    {
        $page = intval($request->page) ? $request->page-1 : 0;
        $date = new \DateTime('last day of this month');
        $skip_month = $page*30;

        $start_date = $date->sub(new \DateInterval("P{$skip_month}M"))->format('Y-m-d');
        $end_date   = $date->sub(new \DateInterval('P30M'))->format('Y-m-d');

        $requests = DB::table('requests')
            ->select(DB::raw('COUNT(*) as cnt, LAST_DAY(created_at) as time'))
            ->whereRaw("DATE(created_at) >= '{$end_date}'")
            ->whereRaw("DATE(created_at) <= '{$start_date}'")
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $attachments = DB::table('attachments')
            ->select(DB::raw('COUNT(*) as cnt, LAST_DAY(created_at) as time'))
            ->whereRaw("date(created_at) >= '{$end_date}'")
            ->whereRaw("DATE(created_at) <= '{$start_date}'")
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $return = [];

        $start = new \DateTime($start_date);
        $end   = new \DateTime($end_date);
        while ($end < $start) {
            $end->modify("last day of next month");

            $today = false;
            if ($end > new \DateTime()) {
                $today = (new \DateTime())->format('Y-m-d');
            }

            $return[$today ? $today : $end->format("Y-m-t")] = [];

            foreach (['requests', 'attachments'] as $elems) {
                foreach ($$elems as $elem) {
                    if ($elem->time == $end->format("Y-m-t")) {
                        $return[$today ? $today : $end->format("Y-m-t")][$elems] = $elem;
                        break;
                    }
                }
            }
        }

        /**
         * сортируем по дате
         */
        return array_reverse($return);
    }

    private function getByYear($request)
    {
        //$page = intval($request->page) ? $request->page-1 : 0;     @todo исправить пагинацию для года, иначе через в 2035 году сломается

        $year_cnt = \App\Models\Request::summaryItemsCount('year');

        $start_of_year = 'first day of july';
        if (date('m') >= 7) {
            $start_of_year = '+1 year '.$start_of_year;
            $year_cnt++;
        }

        $period_start_date = new \DateTime($start_of_year);
        $period_end_date   = (new \DateTime($start_of_year))->sub(new \DateInterval('P1Y'));

        for ($i = 1; $i < $year_cnt; $i++) {
            $start_date = $period_start_date->format('Y-m-d');
            $end_date   = $period_end_date->format('Y-m-d');

            $requests = DB::table('requests')
                ->select(DB::raw('COUNT(*) as cnt'))
                ->whereRaw("DATE(created_at) >= '{$end_date}'")
                ->whereRaw("DATE(created_at) < '{$start_date}'")
                ->get();

            $attachments = DB::table('attachments')
                ->select(DB::raw('COUNT(*) as cnt'))
                ->whereRaw("DATE(created_at) >= '{$end_date}'")
                ->whereRaw("DATE(created_at) < '{$start_date}'")
                ->get();


            if ($period_start_date > new \DateTime()) {
                $start_date = (new \DateTime())->format('Y-m-d');
            }

            $return[$start_date] = []; // для лоопа в ангуляре все даты.

            foreach (['requests', 'attachments' /*, 'clients' */] as $elems) {
                foreach ($$elems as $elem) {
                    if ($elem->cnt) {
                        $return[$start_date][$elems] = $elem;
                    }
                }
            }

            $period_start_date->sub(new \DateInterval("P1Y"));
            $period_end_date->sub(new \DateInterval("P1Y"));
        }

        return $return;
    }
}
