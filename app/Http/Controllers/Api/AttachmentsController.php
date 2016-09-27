<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Attachment;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Service\AttachmentError;

class AttachmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = isset($_COOKIE['attachments']) ? json_decode($_COOKIE['attachments']) : (object)[];

        return [
            'counts' => Attachment::counts($search),
            'data'   => Attachment::search($search)->paginate(30)
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
        try {
            return Attachment::create ($request->input())->fresh();
        } catch (\Illuminate\Database\QueryException $e) {
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Attachment::with(['client'])->find($id);
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
        Attachment::where('id', $id)->update($request->input());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Attachment::destroy($id);
    }

    public function stats(Request $request)
    {
        return Attachment::getStatsByMonth($request->month);
    }

    public function newest()
    {
        $data = Attachment::with(['client', 'tutor'])->newest()->orderBy('date', 'desc')->paginate(10);
        $data->getCollection()->map(function($item) {
            $item->append('link');
            $item->append('account_data_count');
            $item->clean_date = $item->getClean('date') . ' 00:00:00';
            return $item;
        });
        return $data;
    }
}
