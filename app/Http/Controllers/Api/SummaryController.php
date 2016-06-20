<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class SummaryController extends Controller
{
    private $columns = [
        'requests',
        'attachments',
        'received',
        'commission',
        'forecast',
        'debt',
        'mutual_debts',
        'active_attachments',
        'new_clients'
    ];

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
                        ->select(DB::raw('COUNT(*) as cnt, DATE(date) as time'))
                        ->whereRaw("date > '{$end_date}'")
                        ->whereRaw("date <= '{$start_date}'")
                        ->groupBy('time')->get();


        $received = DB::table('accounts')
                        ->select(DB::raw('sum(received) as sum, date_end as time'))
                        ->whereRaw("date_end > '{$end_date}'")
                        ->whereRaw("date_end <= '{$start_date}'")
                        ->groupBy('time')->get();

        $mutual_debts = DB::connection('egecrm')->table('teacher_payments')
                        ->select(DB::raw('sum(sum) as sum, STR_TO_DATE(date, \'%d.%c.%Y\') as time'))
                        ->where('id_status', Account::MUTUAL_DEBT_STATUS)
                        ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') > '{$end_date}'")
                        ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') <= '{$start_date}'")
                        ->groupBy('time')->get();

        $commission = DB::table('account_datas')
                        ->select(DB::raw('sum(if(commission > 0, commission, 0.25*sum)) as sum, date as time'))
                        ->whereRaw("date > '{$end_date}'")
                        ->whereRaw("date <= '{$start_date}'")
                        ->groupBy('time')->get();

        $forecast = DB::table('summaries')
                        ->select(DB::raw('forecast as sum, date as time'))
                        ->whereRaw("date > '{$end_date}'")
                        ->whereRaw("date <= '{$start_date}'")
                        ->groupBy('time')
                        ->groupBy('forecast')
                        ->get();

        $debt = DB::table('summaries')
                        ->select(DB::raw('debt as sum, date as time'))
                        ->whereRaw("date > '{$end_date}'")
                        ->whereRaw("date <= '{$start_date}'")
                        ->groupBy('time')
                        ->groupBy('debt')
                        ->get();

        $active_attachments = DB::table('summaries')
                                    ->select(DB::raw('active_attachments as sum, date as time'))
                                    ->whereRaw("date > '{$end_date}'")
                                    ->whereRaw("date <= '{$start_date}'")
                                    ->groupBy('time')
                                    ->groupBy('active_attachments')
                                    ->get();

        $new_clients        = DB::table('summaries')
                                    ->select(DB::raw('new_clients as sum, date as time'))
                                    ->whereRaw("date > '{$end_date}'")
                                    ->whereRaw("date <= '{$start_date}'")
                                    ->groupBy('time')
                                    ->groupBy('new_clients')
                                    ->get();

        /**
         * @notice (new \DateTime($start_date))->add($interval) :  add($interval) чтобы последняя дата тоже вошла.
         **/
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod(new \DateTime($end_date), $interval, (new \DateTime($start_date))->add($interval), \DatePeriod::EXCLUDE_START_DATE);

        $return = [];
        foreach ($period as $dt) {
            $return[$dt->format("Y-m-d")] = []; // для лоопа в ангуляре все даты.
            foreach ($this->columns as $elems) {
                foreach ($$elems as $elem) {
                    if ($elem->time == $dt->format("Y-m-d")) {
                        $return[$dt->format("Y-m-d")][$elems] = $elem;
                        break;
                    }
                }
            }
        }

        // сегодня
        $return[now(true)] = [
            'active_attachments' => [
                'sum' => Attachment::active()->count(),
            ],
            'new_clients'       => [
                'sum' => Attachment::newest()->count(),
            ]
        ];

        /**
         * сортируем по дате
         */
        return array_reverse($return);
    }

    private function getByWeek($request)
    {
        $page = intval($request->page) ? $request->page-1 : 0;
        $date = new \DateTime('sunday');
        $skip_days = $page*7*($page?31:30);

        $end_date   = clone $date->sub(new \DateInterval("P{$skip_days}D"));
        $start_date = clone $date->sub(new \DateInterval('P210D'));

        $return = [];
        while ($start_date <= $end_date) {
            $week_end = clone $start_date;
            $week_end->modify('+1 week');

            $start = $start_date->format('Y-m-d');
            $end = $week_end->format('Y-m-d');

            $requests = DB::table('requests')
                        ->whereRaw("DATE(created_at) >  '{$start}'")
                        ->whereRaw("DATE(created_at) <= '{$end}'")
                        ->count();

            $attachments = DB::table('attachments')
                           ->where('date', '>',$start)
                           ->where('date', '<=', $end)
                           ->count();

            $received = DB::table('accounts')
                        ->whereRaw("date_end > '{$start}'")
                        ->whereRaw("date_end <= '{$end}'")
                        ->sum('received');

            $mutual_debts = DB::connection('egecrm')->table('teacher_payments')
                            ->where('id_status', Account::MUTUAL_DEBT_STATUS)
                            ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') > '{$start}'")
                            ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') <= '{$end}'")
                            ->sum('sum');

            $commission = DB::table('account_datas')
                          ->where('date', '>', $start)
                          ->where('date', '<=', $end)
                          ->select(DB::raw('sum(if(commission > 0, commission, 0.25*sum)) as sum'))->first()->sum;

            $summary = DB::table('summaries')->where('date', $week_end)->first();

            $forecast = $summary ? $summary->forecast : 0;
            $debt     = $summary ? $summary->debt : 0;
            $active_attachments = $summary ? $summary->active_attachments : 0;
            $new_clients  = $summary ? $summary->new_clients : 0;

            $today = new \DateTime();
            $return_date = $week_end > $today ? $today->format('Y-m-d') : $end;
            $return[$return_date] = [];

            foreach ($this->columns as $elem) {
                $return[$return_date][$elem] = ['cnt' => $$elem, 'sum' => $$elem];
            }
            $start_date->modify('+1 week');
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

        $start_date = $date->sub(new \DateInterval("P{$skip_month}M"))->format('Y-m-t');
        $end_date   = $date->sub(new \DateInterval('P30M'))->format('Y-m-d');

        $requests = DB::table('requests')
                        ->select(DB::raw('COUNT(*) as cnt, LAST_DAY(created_at) as time'))
                        ->whereRaw("DATE(created_at) >= '{$end_date}'")
                        ->whereRaw("DATE(created_at) <= '{$start_date}'")
                        ->groupBy(DB::raw('YEAR(created_at)'))
                        ->groupBy(DB::raw('MONTH(created_at)'))
                        ->get();

        $attachments = DB::table('attachments')
                            ->select(DB::raw('COUNT(*) as cnt, LAST_DAY(date) as time'))
                            ->whereRaw("date >= '{$end_date}'")
                            ->whereRaw("date <= '{$start_date}'")
                            ->groupBy(DB::raw('YEAR(date)'))
                            ->groupBy(DB::raw('MONTH(date)'))
                            ->get();

        $received = DB::table('accounts')
                        ->select(DB::raw('sum(received) as sum, LAST_DAY(date_end) as time'))
                        ->whereRaw("date_end > '{$end_date}'")
                        ->whereRaw("date_end <= '{$start_date}'")
                        ->groupBy(DB::raw('YEAR(date_end)'))
                        ->groupBy(DB::raw('MONTH(date_end)'))
                        ->get();

        $mutual_debts = DB::connection('egecrm')->table('teacher_payments')
                        ->select(DB::raw("sum(sum) as sum, LAST_DAY(STR_TO_DATE(date, '%d.%c.%Y')) as time"))
                        ->where('id_status', Account::MUTUAL_DEBT_STATUS)
                        ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') > '{$end_date}'")
                        ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') <= '{$start_date}'")
                        ->groupBy(DB::raw('YEAR(time)'))
                        ->groupBy(DB::raw('MONTH(time)'))
                        ->get();

        $commission = DB::table('account_datas')
                        ->select(DB::raw('sum(if(commission > 0, commission, 0.25*sum)) as sum, LAST_DAY(date) as time'))
                        ->whereRaw("date > '{$end_date}'")
                        ->whereRaw("date <= '{$start_date}'")
                        ->groupBy(DB::raw('YEAR(date)'))
                        ->groupBy(DB::raw('MONTH(date)'))
                        ->get();

        $forecast = DB::table(DB::raw('(select * from summaries order by date desc) as s'))
                        ->select(DB::raw('forecast as sum, date as time'))
                        ->whereRaw("date > '{$end_date}'")
                        ->whereRaw("date <= '{$start_date}'")
                        ->groupBy(DB::raw('YEAR(date)'))
                        ->groupBy(DB::raw('MONTH(date)'))
                        ->orderBy('date', 'desc')
                        ->get();

        $debt = DB::table(DB::raw('(select * from summaries order by date desc) as s'))
                        ->select(DB::raw('debt as sum, date as time'))
                        ->whereRaw("date > '{$end_date}'")
                        ->whereRaw("date <= '{$start_date}'")
                        ->groupBy(DB::raw('YEAR(date)'))
                        ->groupBy(DB::raw('MONTH(date)'))
                        ->orderBy('date', 'desc')
                        ->get();

        $active_attachments = DB::table(DB::raw('(select * from summaries order by date desc) as s'))
                                ->select(DB::raw('active_attachments as sum, date as time'))
                                ->whereRaw("date > '{$end_date}'")
                                ->whereRaw("date <= '{$start_date}'")
                                ->groupBy(DB::raw('YEAR(date)'))
                                ->groupBy(DB::raw('MONTH(date)'))
                                ->orderBy('date', 'desc')
                                ->get();

        $new_clients = DB::table(DB::raw('(select * from summaries order by date desc) as s'))
                                ->select(DB::raw('new_clients as sum, date as time'))
                                ->whereRaw("date > '{$end_date}'")
                                ->whereRaw("date <= '{$start_date}'")
                                ->groupBy(DB::raw('YEAR(date)'))
                                ->groupBy(DB::raw('MONTH(date)'))
                                ->orderBy('date', 'desc')
                                ->get();

        $return = [];

        $start = new \DateTime($start_date);
        $end   = new \DateTime($end_date);
        while ($end < $start) {
            $end->modify("last day of next month");

            $return_date = $end > new \DateTime() ? (new \DateTime())->format('Y-m-d') : $end->format("Y-m-t");
            $return[$return_date] = [];

            foreach ($this->columns as $elems) {
                foreach ($$elems as $elem) {
                    if ($elem->time == $end->format("Y-m-t")) {
                        $return[$return_date][$elems] = $elem;
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
        $year_cnt = \App\Models\Request::summaryItemsCount('year');

        $start_of_year = '15 july';
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
                            ->whereRaw("date >= '{$end_date}'")
                            ->whereRaw("date < '{$start_date}'")
                            ->get();

            $received = DB::table('accounts')
                            ->select(DB::raw('sum(received) as sum'))
                            ->whereRaw("date_end > '{$end_date}'")
                            ->whereRaw("date_end <= '{$start_date}'")
                            ->get();

            $mutual_debts = DB::connection('egecrm')->table('teacher_payments')
                            ->select(DB::raw("sum(sum) as sum"))
                            ->where('id_status', Account::MUTUAL_DEBT_STATUS)
                            ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') > '{$end_date}'")
                            ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') <= '{$start_date}'")
                            ->get();

            $commission = DB::table('account_datas')
                            ->select(DB::raw('sum(if(commission > 0, commission, 0.25*sum)) as sum'))
                            ->whereRaw("date > '{$end_date}'")
                            ->whereRaw("date <= '{$start_date}'")
                            ->get();

            $forecast = DB::table('summaries')
                            ->select('forecast as sum')
                            ->where('date', $start_date)
                            ->orderBy('date', 'desc')
                            ->get();

            $debt = DB::table('summaries')
                            ->select('debt as sum')
                            ->where('date', $start_date)
                            ->orderBy('date', 'desc')
                            ->get();

            $active_attachments = DB::table('summaries')
                                    ->select('active_attachments as sum')
                                    ->where('date', $start_date)
                                    ->orderBy('date', 'desc')
                                    ->get();

            $new_clients = DB::table('summaries')
                                    ->select('new_clients as sum')
                                    ->where('date', $start_date)
                                    ->orderBy('date', 'desc')
                                    ->get();

            if ($period_start_date > new \DateTime()) {
                $start_date = (new \DateTime())->format('Y-m-d');
            }

            $return[$start_date] = []; // для лоопа в ангуляре все даты.

            foreach ($this->columns as $elems) {
                foreach ($$elems as $elem) {
                        $return[$start_date][$elems] = $elem;
                }
            }

            $period_start_date->sub(new \DateInterval("P1Y"));
            $period_end_date->sub(new \DateInterval("P1Y"));
        }

        return $return;
    }

    public function payments(Request $request)
    {
        $return = [];

        switch ($request->filter) {
            case 'week':
                $return = $this->getPaymentsByWeek($request);
                break;
            case 'month':
                $return = $this->getPaymentsByMonth($request);
                break;
            case 'year':
                $return = $this->getPaymentsByYear($request);
                break;
            case 'day':
            default:
                $return = $this->getPaymentsByDay($request);
        }
        echo json_encode($return);
    }

    private function getPaymentsByDay($request)
    {
        $page = intval($request->page) ? $request->page-1 : 0;
        $date = new \DateTime();
        $start_date = $date->sub(new \DateInterval("P{$page}M"))->format('Y-m-d');

        $date->sub(new \DateInterval('P1M'));
        $end_date = $date->format('Y-m-d');

        $received = DB::table('accounts')
                        ->select(DB::raw('sum(received) as sum, payment_method, date_end as date'))
                        ->whereRaw("date_end > '{$end_date}'")
                        ->whereRaw("date_end <= '{$start_date}'")
                        ->groupBy('date_end')
                        ->groupBy('payment_method')
                        ->get();

        $mutual_debts = DB::connection('egecrm')->table('teacher_payments')
                            ->select(DB::raw('sum(sum) as sum, STR_TO_DATE(date, \'%d.%c.%Y\') as date'))
                            ->where('id_status', Account::MUTUAL_DEBT_STATUS)
                            ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') > '{$end_date}'")
                            ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') <= '{$start_date}'")
                            ->groupBy('date')->get();

        /**
         * @notice (new \DateTime($start_date))->add($interval) :  add($interval) чтобы последняя дата тоже вошла.
         **/
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod(new \DateTime($end_date), $interval, (new \DateTime($start_date))->add($interval), \DatePeriod::EXCLUDE_START_DATE);

        $return = [];
        foreach ($period as $dt) {
            $total = 0;
            $return[$dt->format("Y-m-d")] = []; // для лоопа в ангуляре все даты.

            foreach ($received as $elem) {
                if ($elem->date == $dt->format("Y-m-d")) {
                    $return[$dt->format("Y-m-d")]['received'][$elem->payment_method] = $elem;
                    $total += $elem->sum;
                }
            }

            foreach ($mutual_debts as $elem) {
                if ($elem->date == $dt->format("Y-m-d")) {
                    $return[$dt->format("Y-m-d")]['mutual_debts'] = $elem;
                    $total += $elem->sum;
                    break;
                }
            }
            $return[$dt->format("Y-m-d")]['total'] = $total;
        }

        /**
         * сортируем по дате
         */
        return array_reverse($return);
    }

    private function getPaymentsByWeek($request)
    {
        $page = intval($request->page) ? $request->page-1 : 0;
        $date = new \DateTime('sunday');
        $skip_days = $page*7*($page?31:30);

        $end_date   = clone $date->sub(new \DateInterval("P{$skip_days}D"));
        $start_date = clone $date->sub(new \DateInterval('P210D'));

        $return = [];
        while ($start_date < $end_date) {
            $week_end = clone $start_date;
            $week_end->modify('+1 week');

            $start = $start_date->format('Y-m-d');
            $end = $week_end->format('Y-m-d');

            $received = DB::table('accounts')
                        ->select(DB::raw("sum(received) as sum, payment_method"))
                        ->whereRaw("date_end > '{$start}'")
                        ->whereRaw("date_end <= '{$end}'")
                        ->groupBy('payment_method')
                        ->get();

            $mutual_debts = DB::connection('egecrm')->table('teacher_payments')
                            ->where('id_status', Account::MUTUAL_DEBT_STATUS)
                            ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') > '{$start}'")
                            ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') <= '{$end}'")
                            ->sum('sum');

            $today = new \DateTime();
            $return_date = $week_end >= $today ? $today->format('Y-m-d') : $end;
            $return[$return_date] = [];
            $total = 0;
            foreach ($received as $elem) {
                $return[$return_date]['received'][$elem->payment_method] = $elem;
                $total += $elem->sum;
            }
            $return[$return_date]['mutual_debts']['sum'] = $mutual_debts;
            $return[$return_date]['total'] = $total + $mutual_debts;
            $start_date->modify('+1 week');
        }

        /**
         * сортируем по дате
         */
        return array_reverse($return);
    }

    private function getPaymentsByMonth($request)
    {
        $page = intval($request->page) ? $request->page-1 : 0;
        $date = new \DateTime('last day of this month');
        $skip_month = $page*30;

        $start_date = $date->sub(new \DateInterval("P{$skip_month}M"))->format('Y-m-t');
        $end_date   = $date->sub(new \DateInterval('P30M'))->format('Y-m-d');

        $received = DB::table('accounts')
                    ->select(DB::raw('sum(received) as sum, payment_method, LAST_DAY(date_end) as time'))
                    ->whereRaw("date_end > '{$end_date}'")
                    ->whereRaw("date_end <= '{$start_date}'")
                    ->groupBy(DB::raw('YEAR(date_end)'))
                    ->groupBy(DB::raw('MONTH(date_end)'))
                    ->groupBy('payment_method')->get();

        $mutual_debts = DB::connection('egecrm')->table('teacher_payments')
                        ->select(DB::raw("sum(sum) as sum, LAST_DAY(STR_TO_DATE(date, '%d.%c.%Y')) as time"))
                        ->where('id_status', Account::MUTUAL_DEBT_STATUS)
                        ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') > '{$end_date}'")
                        ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') <= '{$start_date}'")
                        ->groupBy(DB::raw('YEAR(time)'))
                        ->groupBy(DB::raw('MONTH(time)'))
                        ->get();

        $return = [];

        $start = new \DateTime($start_date);
        $end   = new \DateTime($end_date);
        while ($end < $start) {
            $end->modify("last day of next month");
            $return_date = $end > new \DateTime() ? (new \DateTime())->format('Y-m-d') : $end->format("Y-m-t");
            $return[$return_date] = [];
            $total = 0;

            foreach ($received as $elem) {
                if ($elem->time == $end->format("Y-m-t")) {
                    $return[$return_date]['received'][$elem->payment_method] = $elem;
                    $total += $elem->sum;
                }
            }

            foreach ($mutual_debts as $elem) {
                if ($elem->time == $end->format("Y-m-d")) {
                    $return[$return_date]['mutual_debts'] = $elem;
                    $total += $elem->sum;
                }
            }
            $return[$return_date]['total'] = $total;
        }

        /**
         * сортируем по дате
         */
        return array_reverse($return);
    }

    private function getPaymentsByYear($request)
    {
        $year_cnt = \App\Models\Request::summaryItemsCount('year');

        $start_of_year = '15 july';
        if (date('m') >= 7) {
            $start_of_year = '+1 year '.$start_of_year;
            $year_cnt++;
        }

        $period_start_date = new \DateTime($start_of_year);
        $period_end_date   = (new \DateTime($start_of_year))->sub(new \DateInterval('P1Y'));

        for ($i = 1; $i < $year_cnt; $i++) {
            $start_date = $period_start_date->format('Y-m-d');
            $end_date   = $period_end_date->format('Y-m-d');

            $received = DB::table('accounts')
                ->select(DB::raw('sum(received) as sum'))
                ->whereRaw("date_end > '{$end_date}'")
                ->whereRaw("date_end <= '{$start_date}'")
                ->groupBy('payment_method')
                ->get();

            $mutual_debts = DB::connection('egecrm')->table('teacher_payments')
                            ->where('id_status', Account::MUTUAL_DEBT_STATUS)
                            ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') > '{$end_date}'")
                            ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') <= '{$start_date}'")
                            ->sum('sum');

            $total = 0;
            if ($period_start_date > new \DateTime()) {
                $start_date = (new \DateTime())->format('Y-m-d');
            }

            foreach ($received as $k => $elem) {
                $return[$start_date]['received'][$k] = $elem;
                $total += $elem->sum;
            }

            $return[$start_date]['mutual_debts'] = ['sum' => $mutual_debts];
            $return[$start_date]['total'] = $total + $mutual_debts;

            $period_start_date->sub(new \DateInterval("P1Y"));
            $period_end_date->sub(new \DateInterval("P1Y"));
        }

        return $return;
    }

}
