<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Payment\Source;
use App\Models\Payment\Expenditure;

class PaymentsController extends Controller
{
    const VIEWS_FOLDER = 'payments.';

    public function index(Request $request)
    {
        if (! allowed(9999)) {
            return view('errors.not_allowed');
        }

        return view(self::VIEWS_FOLDER . 'index')->with(ngInit([
            'current_page' => $request->page,
            'fresh_payment'=> new Payment,
            'sources'      => Source::orderBy('position')->select('id', 'name')->get(),
            'expenditures' => Expenditure::orderBy('position')->select('id', 'name')->get(),
        ]));
    }

    public function remainders(Request $request)
    {
        // кол-во элементов = кол-во дней с момента самого раннего состояния счета / Source::PER_PAGE_REMAINDERS
        $earliest_remainder_date = Source::whereNotNull('remainder_date')->min('remainder_date');
        $datediff = time() - $earliest_remainder_date;
        $item_cnt = floor($datediff / (60 * 60 * 24) / Source::PER_PAGE_REMAINDERS);

        return view(self::VIEWS_FOLDER . 'remainders')->with(ngInit([
            'page' => $request->page,
            'sources' => collect(Source::get())->keyBy('id')->all(),
            'item_cnt' => $item_cnt,
        ]));
    }
}
