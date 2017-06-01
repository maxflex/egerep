<?php

namespace App\Http\Controllers\Api;

use App\Console\Commands\CalcSummary;
use App\Models\Account;
use App\Models\Attachment;
use App\Models\User;
use App\Models\Debt;
use App\Models\Tutor;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\EfficencyData;
use App\Models\Helpers\MutualPayment;
use DB;

class SummaryController extends Controller
{
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

        $account_payments = DB::table('account_payments')
                                ->whereRaw("date >= '{$start}'")
                                ->whereRaw("date <= '{$end}'")
                                ->sum('sum') + MutualPayment::betweenDates($start, $end)->sum('sum');

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
            'account_payments' => [
                'sum' => $account_payments
            ],
            'debts' => [
                'sum' => Debt::sum([
                    'date_start' => $start,
                    'date_end' => $end,
                    'after_last_meeting' => 1,
                    'debtor' => 0,
                ])
            ],
            'total_debts' => [
                'sum' => Debt::sum([
                    'date_start' => $start,
                    'date_end' => $end,
                    'after_last_meeting' => 0,
                    'debtor' => 0,
                ])
            ],
            'commission' => [
                'sum' => $commission
            ]
        ];


        /** если текущий день -> summary не подсчитан -> считаем текущие значения */
        /** не аддитивные величины **/
        if (new \DateTime($end) >= new \DateTime('today')) {
            extract(CalcSummary::calcData(now(true)));
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
        $account_payments = DB::table('accounts')
                        ->select(DB::raw("sum(account_payments.sum) as sum, account_payments.method"))
                        ->leftJoin('account_payments', 'account_payments.account_id', '=', 'accounts.id')
                        ->whereRaw("date_end >= '{$start}'")
                        ->whereRaw("date_end <= '{$end}'")
                        ->groupBy('account_payments.method')
                        ->get();

        $mutual_payments = MutualPayment::betweenDates($start, $end)->sum('sum');

        $total = 0;
        foreach ($account_payments as $payment) {
            $return['account_payments'][$payment->method] = $payment;
            $total += $payment->sum;
        }

        $return['mutual_payments']['sum'] = $mutual_payments;
        $return['total'] = $total + $mutual_payments;
        return $return;
    }

    private function _getDebtorsData($start, $end)
    {
        return [
            'cnt' => count(Debt::select(DB::raw(1))->where('date', '>=', $start)->where('date', '<=', $end)->where('debtor', 1)->where('after_last_meeting', 1)->groupBy('tutor_id')->get()),
            'sum' => Debt::debtors($start, $end)
        ];
    }

    public function users(Request $request)
    {
        $return = [];
        // для стобца «всего»
        $total_efficency = [
            'conversion' => ['numerator' => 0, 'denominator' => 0],
            'forecast'   => ['numerator' => 0, 'denominator' => 0],
        ];

        $date_from = fromDotDate($request->date_from ?: Carbon::today()->firstOfMonth()->format('d.m.Y'));
        $date_to = fromDotDate($request->date_to ?: Carbon::parse($date_from)->lastOfMonth()->format('d.m.Y'));
        $user_ids = $request->user_ids ?: [];

        $dataQuery = EfficencyData::whereBetween('date', [$date_from, $date_to])->groupBy('group_key');
        $total_commission_query = Attachment::query()->without(['archive', 'review'])
                                            ->select(\DB::raw('round(sum(if(commission > 0, commission, ' . Account::DEFAULT_COMMISSION . ' * sum))) as `sum`'))
                                            ->where('attachments.date', '>=', fromDotDate($date_from))
                                            ->where('attachments.date', '<=', fromDotDate($date_to))
                                            ->join('account_datas', function($join) {
                                                $join->on('attachments.tutor_id', '=', 'account_datas.tutor_id')
                                                     ->on('attachments.client_id', '=', 'account_datas.client_id');
                                            });

        if (count($user_ids)) {
            $dataQuery->whereIn('user_id', $user_ids);
            $total_commission_query->whereIn('attachments.user_id', $user_ids);
        }

        if ($request->type == 'months') {
            $dataQuery->select(['date', \DB::raw("date_format(date, '%m.%y') as group_key")]);
            $commission_query = static::cloneQuery($total_commission_query)->addSelect([
                \DB::raw("date_format(account_datas.date, '%m.%y') as group_key"),
            ])->groupBy('group_key');
        } else {
            $dataQuery->select(['user_id', \DB::raw("user_id as group_key")]);
            $commission_query = static::cloneQuery($total_commission_query)->addSelect([
                \DB::raw('attachments.user_id as group_key'),
            ])->groupBy('group_key');
        }

        // заявки
        foreach(\App\Models\Request::$states as $request_state) {
            $dataQuery->addSelect(\DB::raw("sum(requests_{$request_state}) as requests_{$request_state}"));
        }
        $dataQuery->addSelect(\DB::raw('sum(requests_total) as requests_total'));
        $dataQuery->addSelect(\DB::raw('sum(requests_total) as requests_total'));

        // стыковки
        $dataQuery->addSelect(\DB::raw('sum(attachments_total) as attachments_total'));
        $dataQuery->addSelect(\DB::raw('sum(attachments_newest) as attachments_newest'));
        $dataQuery->addSelect(\DB::raw('sum(attachments_active) as attachments_active'));
        $dataQuery->addSelect(\DB::raw('sum(attachments_archived_no_lessons) as attachments_archived_no_lessons'));
        $dataQuery->addSelect(\DB::raw('sum(attachments_archived_one_lesson) as attachments_archived_one_lesson'));
        $dataQuery->addSelect(\DB::raw('sum(attachments_archived_two_lessons) as attachments_archived_two_lessons'));
        $dataQuery->addSelect(\DB::raw('sum(attachments_archived_three_or_more_lessons) as attachments_archived_three_or_more_lessons'));
        $dataQuery->addSelect(\DB::raw('sum(conversion_denominator) as conversion_denominator'));
        $dataQuery->addSelect(\DB::raw('sum(forecast) as forecast'));

        $commissions = $commission_query->get()->keyBy('group_key');
        foreach ($dataQuery->get()->keyBy('group_key') as $group_key => $data) {
            $total_commission = ($c = $commissions->get($group_key)) ? $c->sum : 0;
            $request_denominator    = ($data->requests_total - $data->requests_reasoned_deny - $data->requests_checked_reasoned_deny) ?: 1;
            $attachments_denominator = $data->attachments_total ?: 1;

            $return_data = [
                'requests'    => [
                    'total'                 => $data->requests_total,
                    'new'                   => $data->requests_new,
                    'awaiting'              => $data->requests_awaiting,
                    'finished'              => $data->requests_finished,
                    'deny'                  => $data->requests_deny,
                    'reasoned_deny'         => $data->requests_reasoned_deny,
                    'checked_reasoned_deny' => $data->requests_checked_reasoned_deny,
                    'deny_percentage'       => round($data->requests_deny * 100 / $request_denominator)
                ],
                'attachments' => [
                    'total'     => $data->attachments_total,
                    'newest'    => $data->attachments_newest,
                    'active'    => $data->attachments_active,
                    'archived'  => [
                        'no_lessons'             => $data->attachments_archived_no_lessons,
                        'one_lesson'             => $data->attachments_archived_one_lesson,
                        'two_lessons'            => $data->attachments_archived_two_lessons,
                        'three_or_more_lessons'  => $data->attachments_archived_three_or_more_lessons,
                    ]
                ],
            ];

            // прогноз
            $forecast_denominator = $return_data['attachments']['active'] + $return_data['attachments']['archived']['three_or_more_lessons'] ?: 1;
            $forecast = round($data->forecast / $forecast_denominator, 2);

            // эффективность
            $conversion_numerator = $return_data['attachments']['active'] + $return_data['attachments']['archived']['three_or_more_lessons']
                + (0.65 * $return_data['attachments']['newest'])
                + (0.1 * $return_data['attachments']['archived']['one_lesson'])
                + (0.15 * $return_data['attachments']['archived']['two_lessons']);
            $conversion_denominator = $data['conversion_denominator'];

            $total_efficency['conversion']['numerator'] += $conversion_numerator;
            $total_efficency['conversion']['denominator'] += $conversion_denominator;
            $total_efficency['forecast']['numerator'] += $data->forecast;
            $total_efficency['forecast']['denominator'] += $forecast_denominator;

            $return_data['efficency'] = [
                'conversion'       => round($conversion_numerator / ($conversion_denominator ?: 1), 2),
                'forecast'         => $forecast,
                'request_avg'      => round(floatval($total_commission) / ($conversion_denominator ?: 1)),
                'attachment_avg'   => round(floatval($total_commission) / ($return_data['attachments']['total'] ?: 1)),
                'total_commission' => round($total_commission)
            ];

            if (is_int($group_key)) { // user_id
                // только если всего заявок != 0 и всего стыковок != 0
                if ($return_data['requests']['total'] > 0 || $return_data['attachments']['total'] > 0) {
                    $return['data'][$group_key == 0 ? 'system' : User::whereId($group_key)->value('login')] = $return_data;
                }
            } else {
                $return['data'][$group_key] = $return_data;
            }
        }

        $return['commissions'] = $total_commission_query->addSelect(\DB::raw("date_format(account_datas.date, '%Y-%m') as account_date"))
                                                        ->groupBy(\DB::raw('account_date'))
                                                        ->get()->pluck('sum', 'account_date');
        if ($request->type == 'months') {
            uksort($return['data'], function ($a, $b) {
                return Carbon::createFromFormat('m.y', $a)->lt(Carbon::createFromFormat('m.y', $b)) ? -1 : 1;
            });
        }

        /**** ВСЕГО ****/
        $total = [];
        foreach($return['data'] as $group_key => $d) {
            @$total['requests']['total'] += $d['requests']['total'];
            @$total['requests']['new'] += $d['requests']['new'];
            @$total['requests']['awaiting'] += $d['requests']['awaiting'];
            @$total['requests']['finished'] += $d['requests']['finished'];
            @$total['requests']['deny'] += $d['requests']['deny'];
            @$total['requests']['reasoned_deny'] += $d['requests']['reasoned_deny'];
            @$total['requests']['checked_reasoned_deny'] += $d['requests']['checked_reasoned_deny'];
            if ($d['requests']['deny_percentage']) {
                @$total['deny_percentage_numerator'] += $d['requests']['deny_percentage'];
                @$total['deny_percentage_denumenator']++;
            }

            @$total['attachments']['total'] += $d['attachments']['total'];
            @$total['attachments']['newest'] += $d['attachments']['newest'];
            @$total['attachments']['active'] += $d['attachments']['active'];
            @$total['attachments']['archived']['no_lessons'] += $d['attachments']['archived']['no_lessons'];
            @$total['attachments']['archived']['one_lesson'] += $d['attachments']['archived']['one_lesson'];
            @$total['attachments']['archived']['two_lessons'] += $d['attachments']['archived']['two_lessons'];
            @$total['attachments']['archived']['three_or_more_lessons'] += $d['attachments']['archived']['three_or_more_lessons'];
            @$total['efficency']['total_commission'] += $d['efficency']['total_commission'];
        }
        $total['requests']['deny_percentage'] = round(@$total['deny_percentage_numerator'] / (@$total['deny_percentage_denumenator']) ?: 1, 2);
        $total['efficency']['conversion'] = round($total_efficency['conversion']['numerator'] / ($total_efficency['conversion']['denominator'] ?: 1), 2);
        $total['efficency']['forecast'] = round($total_efficency['forecast']['numerator'] / ($total_efficency['forecast']['denominator'] ?: 1), 2);
        $total['efficency']['request_avg'] = round(@$total['efficency']['total_commission'] / ($total_efficency['conversion']['denominator'] ?: 1), 2);
        $total['efficency']['attachment_avg'] = round(@$total['efficency']['total_commission'] / (@$total['attachments']['total'] ?: 1), 2);
        $return['data']['всего'] = $total;
        /**** \ВСЕГО ****/

        return $return;
    }

    /**
     * @todo for @user shamshod
     * - optimize
     * */
    public function explain(Request $request)
    {
        @extract(array_filter($request->all()));

        $date_from  = fromDotDate($request->date_from ?: Carbon::today()->firstOfMonth()->format('d.m.Y'));
        $date_to    = fromDotDate($request->date_to ?: Carbon::parse($date_from)->lastOfMonth()->format('d.m.Y'));
        $user_ids   = $request->user_ids ?: [];

        $request_query = \App\Models\Request::query();
        $attachments_with_request_list = Attachment::query()->join('request_lists as rl', 'request_list_id', '=', 'rl.id')->without(['review'])->with('tutor');
        $request_attachments_without_users = \App\Models\Request::query()->join('request_lists as rl', 'rl.request_id', '=', 'requests.id')->join('attachments', 'request_list_id', '=', 'rl.id');

        if (isset($date_from)) {
            $request_query->where('created_at', '>=', fromDotDate($date_from));
            $attachments_with_request_list->where('attachments.date', '>=', fromDotDate($date_from));
        }
        if (isset($date_to)) {
            $request_query->where('created_at', '<=', fromDotDate($date_to) . ' 23:59:59');
            $attachments_with_request_list->where('attachments.date', '<=', fromDotDate($date_to));
        }
        if (count($user_ids)) {
            $request_query->whereIn('requests.user_id', $user_ids);
            $attachments_with_request_list->whereIn('attachments.user_id', $user_ids);
        }

        $effiency_data = [];

        $request_query->where('state', 'deny'); // берем отказные заявки + со стыковками рабочие/заверщенные(3+)
        $request_ids = $request_query->pluck('id')->merge(self::cloneQuery($attachments_with_request_list)->pluck('request_id'))->unique();
        $requests = \App\Models\Request ::whereIn('id', $request_ids)->select(['id', 'user_id', 'state'])->get()->toArray();

        foreach ($requests as $request) {
            $request['attachments'] = [];
            $request_attachments = self::cloneQuery($attachments_with_request_list)->where('request_id', $request['id']);
            $request_attachments_count = self::cloneQuery($request_attachments_without_users)->where('request_id', $request['id'])->count();

            $has_no_lesson = self::cloneQuery($request_attachments)->archived()->hasLessonsWithMissing('=0')->select('attachments.*')->get();
            foreach ($has_no_lesson as $attachment) {
                $attachment->rate = 0;
                $attachment->share = round(1 / $request_attachments_count, 2);
                $request['attachments'][] = $attachment;
            }

            $has_one_lesson = self::cloneQuery($request_attachments)->archived()->hasLessonsWithMissing('=1')->select('attachments.*')->get();
            foreach ($has_one_lesson as $attachment) {
                $attachment->rate = 0.1;
                $attachment->share = round(1 / $request_attachments_count, 2);
                $request['attachments'][] = $attachment;
            }

            $has_two_lesson = self::cloneQuery($request_attachments)->archived()->hasLessonsWithMissing('=2')->select('attachments.*')->get();
            foreach ($has_two_lesson as $attachment) {
                $attachment->rate = 0.15;
                $attachment->share = round(1 / $request_attachments_count, 2);
                $request['attachments'][] = $attachment;
            }

            $has_three_lesson = self::cloneQuery($request_attachments)->archived()->hasLessonsWithMissing('>=3')->select('attachments.*')->get();
            foreach ($has_three_lesson as $attachment) {
                $attachment->rate = 1;
                $attachment->share = round(1 / $request_attachments_count, 2);
                $request['attachments'][] = $attachment;
            }

            $active = self::cloneQuery($request_attachments)->active()->select('attachments.*')->get();
            foreach ($active as $attachment) {
                $attachment->rate = 1;
                $attachment->share = round(1 / $request_attachments_count, 2);
                $request['attachments'][] = $attachment;
            }

            $newest = self::cloneQuery($request_attachments)->newest()->select('attachments.*')->get();
            foreach ($newest as $attachment) {
                $attachment->rate = 0.65;
                $attachment->share = round(1 / $request_attachments_count, 2);
                $request['attachments'][] = $attachment;
            }

            $effiency_data[] = $request;
        }

        return $effiency_data;
    }

    //
    // @todo: обновить на сервере до 7 и использовать (clone $query)-> ...
    //
    private static function cloneQuery($query)
    {
        return clone $query;
    }
}
