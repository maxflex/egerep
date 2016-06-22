<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Client;
use App\Http\Controllers\Controller;

class RequestsController extends Controller
{
    /**
     * Get request state counts
     */
    public function counts(Request $request)
    {
        return [
            'request_state_counts'  => \App\Models\Request::stateCounts($request->user_id),
            'user_counts'           => \App\Models\Request::userCounts($request->state),
        ];
    }

    /**
     * Переместить заявку
     */
    public function transfer($id, Request $request)
    {
        $client_id = $request->client_id;

        $client = Client::where('id', $client_id);

        if ($client->exists()) {
            $r = \App\Models\Request::find($id);
            $r->client_id = $client_id;
            if ($r->lists) {
                foreach($r->lists as $list) {
                    if ($list->attachments) {
                        foreach($list->attachments as $attachment) {
                            \App\Models\AccountData::where('tutor_id', $attachment->tutor_id)
                                ->where('client_id', $attachment->client_id)
                                ->update([
                                    'client_id' => $client_id
                                ]);
                            $attachment->client_id = $client_id;
                            $attachment->save();
                        }
                    }
                }
            }
            $r->save();
            // удалить клиентов без заявок
            Client::removeWithoutRequests();
            return 1;
        } else {
            return null;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return \App\Models\Request::searchByState($request->state)->searchByUser($request->user_id)
                                    ->with(['client'])
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(20)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request = \App\Models\Request::create($request->input());
        return \App\Models\Request::find($request->id);
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
        \App\Models\Request::find($id)->update($request->input());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return \App\Models\Request::destroy($id);
    }
}
