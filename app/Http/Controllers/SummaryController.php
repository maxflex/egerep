<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tutor;
use App\Models\Debt;
use App\Models\Service\Settings;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SummaryController extends Controller
{
    const PER_PAGE = 30;

    /**
     * string $filter       days|weeks|month|year.
     * посчитаем количество элементов пагинации в зависимости от фильтра.
     */
    public function index(Request $request, $filter = 'day')
    {
        if (! allowed(\Shared\Rights::ER_SUMMARY)) {
            return view('errors.not_allowed');
        }
        $item_cnt = \App\Models\Request::summaryItemsCount($filter);

        return view('summary.index', [
                        'item_cnt' => $item_cnt,
                        'per_page' => self::PER_PAGE,
                    ])->with(
                        ngInit([
                            'page'          => $request->page,
                            'filter'        => $filter,
                            'debt_sum'      => Debt::sum([
                                'debtor' => 0,
                                'after_last_meeting' => 1
                            ]),
                            'debt_updating' => Settings::get('debt_updating'),
                            'debt_updated'  => Settings::get('debt_updated'),
                            'type'          => 'total'
                        ])
                    );
    }

    public function payments(Request $request, $filter = 'day')
    {
        if (! allowed(\Shared\Rights::ER_SUMMARY)) {
            return view('errors.not_allowed');
        }
        $item_cnt = \App\Models\Account::summaryItemsCount($filter);

        return view('summary.index', [
                        'item_cnt' => $item_cnt,
                        'per_page' => self::PER_PAGE,
                    ])->with(
                        ngInit([
                            'page'          => $request->page,
                            'filter'        => $filter,
                            'debt_sum'      => Debt::sum([
                                'debtor' => 0,
                                'after_last_meeting' => 1
                            ]),
                            'debt_updating' => Settings::get('debt_updating'),
                            'debt_updated'  => Settings::get('debt_updated'),
                            'type'          => 'payments'
                        ])
                    );
    }

    public function debtors(Request $request, $filter = 'year')
    {
        $item_cnt = \App\Models\Account::summaryItemsCount($filter);

        return view('summary.index', [
                        'item_cnt' => $item_cnt,
                        'per_page' => self::PER_PAGE,
                    ])->with(
                        ngInit([
                            'page'          => $request->page,
                            'filter'        => $filter,
                            'debt_sum'      => Debt::sum([
                                'debtor' => 0,
                                'after_last_meeting' => 1
                            ]),
                            'debt_updating' => Settings::get('debt_updating'),
                            'debt_updated'  => Settings::get('debt_updated'),
                            'type'          => 'debtors'
                        ])
                    );
    }

    public function users()
    {
        if (! allowed(\Shared\Rights::ER_SUMMARY_USERS)) {
            return view('errors.not_allowed');
        }
        return view('summary.users')->with(
            ngInit([
                'debt_sum'      => Debt::sum([
                    'debtor' => 0,
                    'after_last_meeting' => 1
                ]),
                'debt_updating' => Settings::get('debt_updating'),
                'debt_updated'  => Settings::get('debt_updated'),
                'allowed_all'   => allowed(\Shared\Rights::ER_SUMMARY_USERS_ALL)
            ])
        );
    }
}
