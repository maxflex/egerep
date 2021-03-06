<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Service\Settings;

class PeriodsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('periods.index')->with(ngInit([
            'type' => 'total',
            'page' => $request->page,
            'account_errors_updated'  => Settings::get('account_errors_updated'),
            'account_errors_updating' => Settings::get('account_errors_updating'),
        ]));
    }

    public function planned(Request $request)
    {
        return view('periods.index')->with(ngInit([
            'type' => 'planned',
            'page' => $request->page,
            'account_errors_updated'  => Settings::get('account_errors_updated'),
            'account_errors_updating' => Settings::get('account_errors_updating'),
        ]));
    }

    public function payments(Request $request)
    {
        return view('periods.index')->with(ngInit([
            'type' => 'payments',
            'page' => $request->page,
            'account_errors_updated'  => Settings::get('account_errors_updated'),
            'account_errors_updating' => Settings::get('account_errors_updating'),
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
