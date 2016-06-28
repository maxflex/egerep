<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Attachment;
use App\Http\Requests;
use App\Http\Controllers\Controller;

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
        $search = filterParams($search);

        /**
         * сделал с join чтобы сортировать
         */
        $query = Attachment::with(['tutor', 'client']);

        if (isset($search->state)) {
            $query->searchByState($search->state);
        }

        $query->join('request_lists as r', 'request_list_id', '=', 'r.id');             /* request_id нужен чтобы генерить правильную ссылку для редактирования */
        $query->leftJoin('archives as a', 'a.attachment_id', '=', 'attachments.id');

        if (isset($search->hide)) {
           $query->where('attachments.hide', $search->hide);
        }

        /**
         * количество занятий - есть и атирибут account_data_count.
         * но чтобы сортировка была правильной, должны через join получить.
         */
        $query->leftJoin('account_datas as ad', function($query) {
            $query->on('ad.tutor_id', '=', 'attachments.tutor_id');
            $query->on('ad.client_id', '=', 'attachments.client_id');
        })->groupBy('attachments.id');

        $query->select(
            'attachments.*', 'r.request_id',
            'a.created_at AS archive_date', 'a.total_lessons_missing',
            \DB::raw('count(ad.id) as lesson_count')
        );

        if (isset($search->account_data)) {
           $query->havingRaw('count(ad.id) ' . ($search->account_data ? '=' : '>') . 0);
        }

        if (isset($search->forecast)) {
            $query->where('attachments.forecast', ($search->forecast ? '=' : '>'), 0);
        }

        if (isset($search->total_lessons_missing)) {
            if ($search->total_lessons_missing) {
                $query->whereNullOrZero('a.total_lessons_missing');
            } else {
                $query->where('a.total_lessons_missing', '>', 0);
            }
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
}
