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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! allowed(9999)) {
            return view('errors.not_allowed');
        }

        return view(self::VIEWS_FOLDER . 'index')->with(ngInit([
            'current_page' => $request->page,
            'fresh_payment'=> new Payment,
            'sources'      => Source::select('id', 'name')->get(),
            'expenditures' => Expenditure::select('id', 'name')->get(),
        ]));
    }
}
