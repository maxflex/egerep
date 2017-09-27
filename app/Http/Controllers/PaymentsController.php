<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Payment\Source;
use App\Models\Payment\Expenditure;
use App\Models\Payment\Addressee;

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
            'sources'      => Source::select('id', 'name')->get(),
            'expenditures' => Expenditure::select('id', 'name')->get(),
            'addressees'   => Addressee::select('id', 'name')->get(),
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! allowed(9999)) {
            return view('errors.not_allowed');
        }
        return view(self::VIEWS_FOLDER . 'create')->with(ngInit([
            'model'        => new Payment(['date' => date('Y-m-d')]),
            'sources'      => Source::select('id', 'name')->get(),
            'expenditures' => Expenditure::select('id', 'name')->get(),
            'addressees'   => Addressee::select('id', 'name')->get(),
        ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! allowed(9999)) {
            return view('errors.not_allowed');
        }
        return view(self::VIEWS_FOLDER . 'edit')->with(ngInit([
            'id'           => $id,
            'sources'      => Source::select('id', 'name')->get(),
            'expenditures' => Expenditure::select('id', 'name')->get(),
            'addressees'   => Addressee::select('id', 'name')->get(),
        ]));
    }
}
