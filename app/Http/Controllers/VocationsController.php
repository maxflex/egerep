<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Cache;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Vocation;

class VocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('vocations.index')->with(ngInit([
            'index'     => true,
            'show'      => true,
            'vocations' => \DB::table('vocations')->select('id', 'user_id', 'approved_by', 'created_at', 'work_off')->orderBy('created_at', 'desc')->get(),
            'vocation'  => Vocation::emptyObject()
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vocations.create')->with(ngInit([
            'vocation' => new Vocation
        ]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('vocations.show')->with(ngInit([
            'vocation' => Vocation::find($id),
            'show'     => true
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
        // @todo: разбить на права
        if (! in_array(\App\Models\User::fromSession()->id, [1, 56, 65, 69])) {
            return redirect()->action('VocationsController@show', compact('id'));
        }
        return view('vocations.edit')->with(ngInit([
            'vocation' => Vocation::find($id),
        ]));
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
