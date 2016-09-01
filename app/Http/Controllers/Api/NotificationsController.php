<?php

namespace App\Http\Controllers\Api;

use App\Models\Attachment;
use Illuminate\Http\Request;

use App\Models\Notification;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        return Notification::where('entity_type', $request->input('entity_type'))
                        ->where('entity_id', $request->input('entity_id'))
                        ->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        $search = isset($_COOKIE['notifications']) ? json_decode($_COOKIE['notifications']) : (object)[];
        return [
            'data'   => Attachment::notificationSearch($search)->paginate(30),
            'counts' => Attachment::notificationCounts($search),
        ];
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
        return Notification::create($request->input());
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
        Notification::find($id)->update($request->input());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Notification::destroy($id);
    }
}
