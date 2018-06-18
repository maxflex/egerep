<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Sms;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Sms::number($request->input('number'))->get();
    }

    public function list(Request $request)
    {
        $query = Sms::orderBy('created_at', 'desc');

        if ($request->search) {
            $query->whereRaw("message LIKE '%{$request->search}%'");
        }

        if (allowed(\Shared\Rights::SECRET_SMS)) {
            if (isset($request->is_secret) && notBlank($request->is_secret)) {
                $query->where('is_secret', $request->is_secret);
            }
        } else {
            $query->where('is_secret', false);
        }

        return $query->paginate(30)->toJson();
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
        extract($request->input());
        return Sms::send($to, $message)->toJson();
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
