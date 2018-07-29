<?php

namespace App\Http\Controllers\Payments;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Payment\Source;

class SourcesController extends Controller
{
    const VIEWS_FOLDER = 'payments.sources.';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! allowed(\Shared\Rights::ER_PAYSTREAM)) {
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
        if (! allowed(\Shared\Rights::ER_PAYSTREAM) || ! allowed(\Shared\Rights::IS_SUPERUSER)) {
            return view('errors.not_allowed');
        }
        return view(self::VIEWS_FOLDER . 'create')->with(ngInit([
            'model' => new Source,
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
        if (! allowed(\Shared\Rights::ER_PAYSTREAM) || ! allowed(\Shared\Rights::IS_SUPERUSER)) {
            return view('errors.not_allowed');
        }
        return view(self::VIEWS_FOLDER . 'edit')->with(ngInit(compact('id')));
    }
}
