<?php

namespace App\Http\Controllers\Payments;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Payment\Expenditure;
use App\Models\Payment\ExpenditureGroup;

class ExpendituresController extends Controller
{
    const VIEWS_FOLDER = 'payments.expenditures.';

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
            'current_page' => $request->page
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
            'model' => new Expenditure,
            'groups'=> ExpenditureGroup::all()
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
        $groups = ExpenditureGroup::all();
        return view(self::VIEWS_FOLDER . 'edit')->with(ngInit(compact('id', 'groups')));
    }
}
