<?php

namespace App\Http\Controllers\Api;

use App\Console\Commands\CalcSummary;
use App\Models\Account;
use App\Models\Attachment;
use App\Models\Tutor;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class SummaryController extends Controller
{
    private $columns = [
        'requests',
        'attachments',
        'archives',
        'received',
        'commission',
        'forecast',
        'debt',
        'mutual_debts',
        'active_attachments',
        'new_clients'
    ];

    /**
     * @todo объединить index и payments методы.
     * @todo пока не знаю как роуты менять чтобы можно было объединить:/
     *
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $return = [];

        $page   = intval($request->page) ? $request->page - 1 : 0;
        switch ($request->filter) {
            case 'week':
                $return = $this->getByWeek('summary', $page);
                break;
            case 'month':
                $return = $this->getByMonth('summary', $page);
                break;
            case 'year':
                $return = $this->getByYear('summary');
                break;
            case 'day':
            default:
                $return = $this->getByDay('summary', $page);
        }
        return array_reverse($return);
    }

    public function payments(Request $request)
    {
        $return = [];

        $page   = intval($request->page) ? $request->page - 1 : 0;
        switch ($request->filter) {
            case 'week':
                $return = $this->getByWeek('payments', $page);
                break;
            case 'month':
                $return = $this->getByMonth('payments', $page);
                break;
            case 'year':
                $return = $this->getByYear('payments');
                break;
            case 'day':
            default:
                $return = $this->getByDay('payments', $page);
        }
        return array_reverse($return);
    }

    public function debtors(Request $request)
    {
        $return = [];

        $page   = intval($request->page) ? $request->page - 1 : 0;
        switch ($request->filter) {
            case 'week':
                $return = $this->getByWeek('debtors', $page);
                break;
            case 'month':
                $return = $this->getByMonth('debtors', $page);
                break;
            case 'year':
                $return = $this->getByYear('debtors');
                break;
            case 'day':
            default:
                $return = $this->getByDay('debtors', $page);
        }
        return array_reverse($return);
    }

    private function getByDay($type, $page)
    {
        $date = new \DateTime('today');
        $skip_days = $page * 30;

        $end_date   = clone $date->sub(new \DateInterval("P{$skip_days}D"));
        $start_date = clone $date->sub(new \DateInterval('P30D'));

        $return = [];
        while ($start_date < $end_date) {
            $start = $start_date->modify('+1 day')->format('Y-m-d'); // переход на новую неделю
            $end   = $start_date->format('Y-m-d');

            $return_date = $end;
            $return[$return_date] = $this->calc($type, $start, $end);
        }

        return $return;
    }

    private function getByWeek($type, $page)
    {
        $date = new \DateTime('sunday');
        $skip_days = $page * 7 * 30;

        $end_date   = clone $date->sub(new \DateInterval("P{$skip_days}D"));
        $start_date = clone $date->sub(new \DateInterval('P210D'));

        $return = [];
        while ($start_date < $end_date) {
            $start = $start_date->modify('+1 day')->format('Y-m-d'); // переход на новую неделю
            $end   = $start_date->modify('sunday')->format('Y-m-d');

            $return_date = $start_date > new \DateTime ? now(true) : $end;
            $return[$return_date] = $this->calc($type, $start, $end);
        }

        return $return;
    }

    private function getByMonth($type, $page)
    {
        $date       = new \DateTime('last day of this month');
        $skip_month = $page * 30;

        $end_date   = clone $date->modify("last day of -$skip_month months");
        $start_date = clone $date->modify('last day of -29 months');

        $return = [];
        while ($start_date < $end_date) {
            $start = $start_date->modify('first day of this month')->format('Y-m-d');
            $end   = $start_date->modify('last day of this month')->format('Y-m-d');

            $return_date = $start_date > new \DateTime ? now(true) : $end;
            $return[$return_date] = $this->calc($type, $start, $end);

            $start_date->modify('first day of next month');
        }

        return $return;
    }

    private function getByYear($type)
    {
        $end_date   = new \DateTime('today');
        $start_date = new \DateTime(\App\Models\Request::orderBy('created_at')->pluck('created_at')->first());

        $return = [];
        while ($start_date < $end_date) {
            $start = $start_date->modify('16 july')->format('Y-m-d');
            $end   = $start_date->modify('+1 year 15 july')->format('Y-m-d');

            $return_date = $start_date > new \DateTime ? now(true) : $end;
            $return[$return_date] = $this->calc($type, $start, $end);
        }

        return $return;
    }

    private function calc($type, $start, $end)
    {
        $return = [];
        switch ($type) {
            case 'debtors':
                $return = $this->_getDebtorsData($start, $end);
                break;
            case 'payments':
                $return = $this->_getPaymentsData($start, $end);
                break;
            case 'summary':
                $return = $this->_getData($start, $end);
                break;
        }

        return $return;
    }

    private function _getData($start, $end)
    {
        $requests = DB::table('requests')
                    ->whereRaw("DATE(created_at) >= '{$start}'")
                    ->whereRaw("DATE(created_at) <= '{$end}'")
                    ->count();

        $attachments = DB::table('attachments')
                        ->whereRaw("DATE(created_at) >= '{$start}'")
                        ->whereRaw("DATE(created_at) <= '{$end}'")
                        ->count();

        $archives = DB::table('archives')
                        ->whereRaw("DATE(created_at) >= '{$start}'")
                        ->whereRaw("DATE(created_at) <= '{$end}'")
                        ->count();

        $received = DB::table('accounts')
                    ->whereRaw("date_end >= '{$start}'")
                    ->whereRaw("date_end <= '{$end}'")
                    ->sum('received');

        $mutual_debts = DB::connection('egecrm')->table('payments')
                        ->where('id_status', Account::MUTUAL_DEBT_STATUS)
                        ->where('entity_type', Tutor::USER_TYPE)
                        ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') >= '{$start}'")
                        ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') <= '{$end}'")
                        ->sum('sum');

        $commission = DB::table('account_datas')
                        ->whereRaw("date >= '{$start}'")
                        ->whereRaw("date <= '{$end}'")
                        ->sum(DB::raw('if(commission > 0, commission, '.Account::DEFAULT_COMMISSION.'*sum)'));

        $data = [
            'requests' => [
                'cnt' => $requests
            ],
            'attachments' => [
                'cnt' => $attachments
            ],
            'archives' => [
                'cnt' => $archives
            ],
            'received' => [
                'sum' => $received
            ],
            'mutual_debts' => [
                'sum' => $mutual_debts
            ],
            'commission' => [
                'sum' => $commission
            ]
        ];


        /** если текущий день -> summary не подсчитан -> считаем текущие значения */
        /** не аддитивные величины **/
        if (new \DateTime($end) >= new \DateTime('today')) {
            extract(CalcSummary::calcData());
            foreach (['forecast', 'debt', 'active_attachments', 'new_clients'] as $field) {
                $data[$field]['sum'] = $$field;
            }
        } else {
            $summary = DB::table('summaries')->where('date', $end)->first();
            if ($summary) {
                foreach (['forecast', 'debt', 'active_attachments', 'new_clients'] as $field) {
                    $data[$field]['sum'] = $summary->$field;
                }
            }
        }

        /**
         * сортируем по дате
         */
        return $data;
    }

    private function _getPaymentsData($start, $end)
    {
        $received = DB::table('accounts')
                    ->select(DB::raw("sum(received) as sum, payment_method"))
                    ->whereRaw("date_end >= '{$start}'")
                    ->whereRaw("date_end <= '{$end}'")
                    ->groupBy('payment_method')
                    ->get();

        $mutual_debts = DB::connection('egecrm')->table('payments')
                        ->where('id_status', Account::MUTUAL_DEBT_STATUS)
                        ->where('entity_type', Tutor::USER_TYPE)
                        ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') >= '{$start}'")
                        ->whereRaw("STR_TO_DATE(date, '%d.%c.%Y') <= '{$end}'")
                        ->sum('sum');

        $total = 0;
        foreach ($received as $elem) {
            $return['received'][$elem->payment_method] = $elem;
            $total += $elem->sum;
        }

        $return['mutual_debts']['sum'] = $mutual_debts;
        $return['total'] = $total + $mutual_debts;
        return $return;
    }

    private function _getDebtorsData($start, $end)
    {
        return DB::table('tutors')
            ->join(DB::raw('(
                SELECT MAX(id) as max_attachment_id, tutor_id
                FROM attachments
                GROUP BY tutor_id
                ) a'), 'a.tutor_id', '=', 'tutors.id')
            ->join(DB::raw('(
                SELECT MAX(date) as last_archive_date, attachment_id
                FROM archives
                GROUP BY attachment_id
                ) ar'), 'ar.attachment_id', '=', 'a.max_attachment_id')
            ->leftJoin(DB::raw('(
                  SELECT MAX(date_end) as last_account_date, debt_calc as last_account_debt, tutor_id
                  FROM accounts
                  GROUP BY tutor_id
                  ) ac'), 'tutors.id', '=', 'ac.tutor_id')
            ->where('debtor', 1)
            ->whereRaw("last_archive_date >= '{$start}'")
            ->whereRaw("last_archive_date <= '{$end}'")
            ->select(DB::raw('count(*) as cnt, sum(debt_calc) as sum, sum(last_account_debt) as debt_sum'))->first();
    }

    public function users(Request $request)
    {
        @extract(array_filter($request->all()));

        $return = [];
        $request_query = \App\Models\Request::query();
        $attachments_query = Attachment::query();
        $commission_query = \App\Models\AccountData::query();

        if (isset($date_from)) {
            $request_query->where('created_at', '>=', fromDotDate($date_from));
            $attachments_query->where('created_at', '>=', fromDotDate($date_from));
            $commission_query->where('account_datas.date', '>=', fromDotDate($date_from));
        }
        if (isset($date_to)) {
            $request_query->where('created_at', '<=', fromDotDate($date_to) . ' 23:59:59');
            $attachments_query->where('created_at', '<=', fromDotDate($date_to));
            $commission_query->where('account_datas.date', '<=', fromDotDate($date_to));
        }

        $attachments_query_without_user = clone $attachments_query;

        if (isset($user_ids)) {
            $request_query->whereIn('user_id', $user_ids);
            $attachments_query->whereIn('user_id', $user_ids);
            $commission_query->join('attachments', function($join) {
                $join->on('attachments.tutor_id', '=', 'account_datas.tutor_id')
                    ->on('attachments.client_id', '=', 'account_datas.client_id');
            })->whereIn('attachments.user_id', $user_ids);
        }

        foreach(\App\Models\Request::$states as $request_state) {
            $return['requests'][$request_state] = self::cloneQuery($request_query)->searchByState($request_state)->count();
        }

        $return['requests']['total'] = $request_query->count();

        $return['attachments'] = [
            'total'    => self::cloneQuery($attachments_query)->count(),
            'newest'   => self::cloneQuery($attachments_query)->newest()->count(),
            'active'   => self::cloneQuery($attachments_query)->active()->count(),
            'archived' => [
                'no_lessons'            => self::cloneQuery($attachments_query)->archived()->noLessons()->count(),
                'one_lesson'            => self::cloneQuery($attachments_query)->archived()->hasLessons('=1')->count(),
                'two_lessons'           => self::cloneQuery($attachments_query)->archived()->hasLessons('=2')->count(),
                'three_or_more_lessons' => self::cloneQuery($attachments_query)->archived()->hasLessons('>=3')->count(),
            ],
        ];
        foreach(\App\Models\User::real()->pluck('id')->all() as $id) {
            $return['attachments']['users'][$id] = self::cloneQuery($attachments_query)->whereUserId($id)->count();
        }

        $return['commissions'] = self::cloneQuery($commission_query)->select(
            'account_datas.date',
            DB::raw('round(sum(if(commission > 0, commission, ' . Account::DEFAULT_COMMISSION . ' * sum))) as `sum`')
        )->groupBy(DB::raw("DATE_FORMAT(account_datas.date, '%Y-%m')"))->get();

        //
        // ЭФФЕКТИВНОСТЬ
        //
        $attachments_query_without_user_count = $attachments_query_without_user->count();
        $numerator = $return['attachments']['active'] + $return['attachments']['archived']['three_or_more_lessons'] + 0.65 * ($return['attachments']['newest']);
        $denominator = $attachments_query_without_user_count;

        // $denominator = 0;
        // if (isset($user_ids)) {
        //     foreach(self::cloneQuery($attachments_query)->select('tutor_id', 'client_id')->groupBy('')->get() as $a) {
        //         $denominator +=
        //     }
        // }
        // $denominator = $attachments_query_without_user_count / $return['attachments']['total'];

        $total_commission = self::cloneQuery($commission_query)->sum(DB::raw('if(commission > 0, commission, ' . Account::DEFAULT_COMMISSION . ' * sum)'));

        $return['efficency'] = [
            'conversion'       => round($numerator / $denominator, 2),
            'forecast'         => round(self::cloneQuery($attachments_query)->avg('forecast')),
            'request_avg'      => round($total_commission / $attachments_query_without_user_count),
            'attachment_avg'   => round($total_commission / $return['attachments']['total']),
            'total_commission' => round($total_commission),
        ];

        return $return;
    }

    //
    // @todo: обновить на сервере до 7 и использовать self::cloneQuery($query)-> ...
    //
    private static function cloneQuery($query)
    {
        return clone $query;
    }
}
