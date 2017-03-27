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
        return [
            'cnt' => count(Debt::select(DB::raw(1))->where('date', '>=', $start)->where('date', '<=', $end)->where('debtor', 1)->where('after_last_meeting', 1)->groupBy('tutor_id')->get()),
            'sum' => Debt::debtors($start, $end)
        ];
    }

    public function users(Request $request)
    {
        $return = [];
        if ($request->type == 'months') {
            // начинаем смотреть от этой даты и вперед
            $date = fromDotDate($request->date_from);
            $end_date = fromDotDate($request->date_to);
            do {
                $new_request = clone $request;
                $new_request['date_from'] = dateFormat($date, true);

                // если дата конца месяца больше даты конца фильтра,
                // то устанавливаем дату конца из фильтра – иначе дату конца месяца итерации
                $end_of_month = Carbon::parse($date)->lastOfMonth();
                if ($end_of_month->toDateString() > $end_date) {
                    $new_request['date_to'] = dateFormat($end_date, true);
                } else {
                    $new_request['date_to'] = $end_of_month->format('d.m.Y');
                }
                $return['data'][Carbon::parse($date)->format('m.y')] = $this->_users($new_request);
                // переходим на начало следующего месяца
                $date = Carbon::parse($date)->addMonth()->firstOfMonth()->toDateString();
            } while ($date < $end_date);
        } else {
            foreach($request->user_ids as $user_id) {
                $new_request = clone $request;
                $new_request['user_ids'] = [$user_id];
                $return['data'][User::whereId($user_id)->value('login')] = $this->_users($new_request);
            }
        }
        $return['commissions'] = $this->_users($request, true);
        return $return;
    }

    /**
     *
     */
    public function _users(Request $request, $commissions = false)
    {
        @extract(array_filter($request->all()));

        $return = [];
        $request_query = \App\Models\Request::query();
        $attachments_query = Attachment::query();
        $request_attachments_without_users = \App\Models\Request::query()
                    ->join('request_lists as rl', 'rl.request_id', '=', 'requests.id')
                    ->join('attachments', 'request_list_id', '=', 'rl.id');

        if (isset($date_from)) {
            $request_query->where('requests.created_at', '>=', fromDotDate($date_from));
            $attachments_query->where('attachments.created_at', '>=', fromDotDate($date_from));
        }
        if (isset($date_to)) {
            $request_query->where('requests.created_at', '<=', fromDotDate($date_to) . ' 23:59:59');
            $attachments_query->where('attachments.created_at', '<=', fromDotDate($date_to) . ' 23:59:59');
        }

        $commission_query = self::cloneQuery($attachments_query)->join('account_datas', function($join) {
            $join->on('attachments.tutor_id', '=', 'account_datas.tutor_id')
                ->on('attachments.client_id', '=', 'account_datas.client_id');
        });

        if (isset($user_ids)) {
            $request_query->whereIn('requests.user_id', $user_ids);
            $attachments_query->whereIn('attachments.user_id', $user_ids);
            $commission_query->whereIn('attachments.user_id', $user_ids);
        }

        // так как это общее для всех – возвращаем только когда нужно
        if ($commissions) {
            return self::cloneQuery($commission_query)->select(
                'account_datas.date',
                DB::raw('round(sum(if(commission > 0, commission, ' . Account::DEFAULT_COMMISSION . ' * sum))) as `sum`')
            )->groupBy(DB::raw("DATE_FORMAT(account_datas.date, '%Y-%m')"))->get();
        }

        foreach(\App\Models\Request::$states as $request_state) {
            $return['requests'][$request_state] = self::cloneQuery($request_query)->searchByState($request_state)->count();
        }

        $return['requests']['total'] = $request_query->count();

        // "доля отказов" без учета "обоснованный отказ" и "подтвержденный обоснованный отказ"
        $denominator = $return['requests']['total'] - $return['requests']['reasoned_deny'] - $return['requests']['checked_reasoned_deny'];
        $return['requests']['deny_percentage'] = $denominator > 0 ? round($return['requests']['deny'] * 100 / $denominator) : 0;

        $return['attachments'] = [
            'total'    => self::cloneQuery($attachments_query)->count(),
            'newest'   => self::cloneQuery($attachments_query)->newest()->count(),
            'active'   => self::cloneQuery($attachments_query)->active()->count(),
            'archived' => [
                'no_lessons'            => self::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('=0')->count(),
                'one_lesson'            => self::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('=1')->count(),
                'two_lessons'           => self::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('=2')->count(),
                'three_or_more_lessons' => self::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('>=3')->count(),
            ],
        ];

        // доля от стыковок
        $denominator = $return['attachments']['total'];
        $return['attachments']['archived']['no_lessons_percentage'] = $denominator > 0 ? round($return['attachments']['archived']['no_lessons'] * 100 / $denominator) : 0;
        $return['attachments']['archived']['one_lesson_percentage'] = $denominator > 0 ? round($return['attachments']['archived']['one_lesson'] * 100 / $denominator) : 0;
        $return['attachments']['archived']['two_lessons_percentage'] = $denominator > 0 ? round($return['attachments']['archived']['two_lessons'] * 100 / $denominator) : 0;

        //
        // ЭФФЕКТИВНОСТЬ
        //
        $numerator = $return['attachments']['active'] + $return['attachments']['archived']['three_or_more_lessons']
                        + (0.65 * $return['attachments']['newest'])
                        + (0.1 * $return['attachments']['archived']['one_lesson'])
                        + (0.15 * $return['attachments']['archived']['two_lessons']);

        $attachments_with_request_list = self::cloneQuery($attachments_query)->join('request_lists as rl', 'request_list_id', '=', 'rl.id')->without(['review', 'archive']);
        $denominator = 0;

        $request_ids = self::cloneQuery($attachments_with_request_list)->pluck('request_id')->unique();
        $requests = \App\Models\Request ::whereIn('id', $request_ids)->select(['id', 'user_id', 'state'])->get()->toArray();
        foreach ($requests as $request) {
            $request_attachments_count = self::cloneQuery($attachments_with_request_list)->where('request_id', $request['id'])->count();
            $request_attachments_count_without_users = self::cloneQuery($request_attachments_without_users)->where('request_id', $request['id'])->count();

            $denominator += $request_attachments_count / $request_attachments_count_without_users;
        }
        $denominator += $return['requests']['deny'];


        $total_commission = self::cloneQuery($commission_query)->sum(DB::raw('if(commission > 0, commission, ' . Account::DEFAULT_COMMISSION . ' * sum)'));


        $forecast_denominator = self::cloneQuery($attachments_query)->active()->count() + self::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('>=3')->count();
        $forecast_numerator = self::cloneQuery($attachments_query)->active()->sum('forecast') + self::cloneQuery($attachments_query)->archived()->hasLessonsWithMissing('>=3')->sum('forecast');
        $forecast = $forecast_denominator ? round($forecast_numerator / $forecast_denominator, 2) : 0;


        $return['efficency'] = [
            'conversion'       => $denominator ? round($numerator / $denominator, 2) : 0,
            'forecast'         => $forecast,
            'request_avg'      => $denominator + $return['requests']['deny'] ? round(floatval($total_commission) / ($denominator + $return['requests']['deny'])) : 0,
            'attachment_avg'   => $return['attachments']['total'] ? round(floatval($total_commission) / $return['attachments']['total']) : 0,
            'total_commission' => round($total_commission)
        ];

        return $return;
    }

    /**
     * @todo for @user shamshod
     * - optimize
     * */
    public function explain(Request $request)
    {
        @extract(array_filter($request->all()));
        $request_query = \App\Models\Request::query();
        $attachments_with_request_list = Attachment::query()->join('request_lists as rl', 'request_list_id', '=', 'rl.id')->without(['review'])->with('tutor');
        $request_attachments_without_users = \App\Models\Request::query()->join('request_lists as rl', 'rl.request_id', '=', 'requests.id')->join('attachments', 'request_list_id', '=', 'rl.id');

        if (isset($date_from)) {
            $request_query->where('created_at', '>=', fromDotDate($date_from));
            $attachments_with_request_list->where('attachments.created_at', '>=', fromDotDate($date_from));
        }
        if (isset($date_to)) {
            $request_query->where('created_at', '<=', fromDotDate($date_to) . ' 23:59:59');
            $attachments_with_request_list->where('attachments.created_at', '<=', fromDotDate($date_to) . ' 23:59:59');
        }
        if (isset($user_ids)) {
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
