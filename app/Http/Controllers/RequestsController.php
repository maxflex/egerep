<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Service\Settings;

class RequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $state_id = 'new')
    {
        return view('requests.index')->with(
            ngInit([
                'page'  => $request->input('page'),
                'errors' => false, // страница ошибок?
                'chosen_state_id' => $state_id,
            ])
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function errors(Request $request)
    {
        if (! allowed(\Shared\Rights::ER_REQUEST_ERRORS)) {
            return view('errors.not_allowed');
        }
        return view('requests.index')->with(
            ngInit([
                'page'  => $request->input('page'),
                'errors' => true, // страница ошибок?
                'chosen_state_id' => 'all',
                'request_errors_updated'  => Settings::get('request_errors_updated'),
                'request_errors_updating' => Settings::get('request_errors_updating')
            ])
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $client = new Client;
        $request = new \App\Models\Request;
        $request->user_id_created = User::id();
        return view('clients.create')->with(
            ngInit([
                'new_client' => $client,
                'new_request' => $request,
                'academic_year' => academicYear(),
            ])
        );
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $request = \App\Models\Request::find($id);
        return view('clients.edit')->with([
            'id' => $request->client_id,
            'request_id' => $request->id,
        ])->with(ngInit([
            'academic_year' => academicYear(),
        ]));
        // return redirect()->route('clients.edit', [
        //     'id' => $request->client_id
        // ]);
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
