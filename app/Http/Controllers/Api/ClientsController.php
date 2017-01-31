<?php

namespace App\Http\Controllers\Api;

use Log;
use App\Models\Client;
use Illuminate\Http\Request;


use App\Http\Requests;
use App\Http\Controllers\Controller;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search_text = substr($request->global_search, 1);
        return Client::findByPhone($search_text)->paginate(30)->toJson();
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
        $client = $request->input();

        /**
         * запонимаем и убираем relations, т. к. при их сохранении нужен client_id
         */
        $client_request = $client['requests'][0];
        $client_markers = $client['markers'];
        unset($client['requests']);
        unset($client['markers']);

        $client = Client::create($client);
        $client->markers = $client_markers;
        $client->update();

        $client_request['client_id'] = $client->id;
        return \App\Models\Request::create($client_request)->toJson();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = Client::find($id);
        $client->requests->map(function($request){
            return $request->lists->map(function($list){
                return $list->append('tutors');
            });
        });

        return $client->toJson();
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
        $client = Client::find($id);
        $client->update($request->input());
        $client = $client->fresh();
        $client->requests->each(function ($request) {
            $request->lists->each(function ($list) {
                $list->append(['tutors']);
            });
        });
        return $client;
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
