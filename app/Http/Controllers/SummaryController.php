<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $item_cnt = \App\Models\Request::summaryItemsCount($filter);

        return view('summary.index', [
                        'item_cnt'  => $item_cnt,
                        'per_page' => self::PER_PAGE
                    ])->with(
                        ngInit([
                            'page'   => $request->page,
                            'filter' => $filter
                        ])
                    );
    }
}
