<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models;
use App\Http\Controllers\Controller;
use App\Models\Service\Log;

class LogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search = isset($_COOKIE['logs']) ? json_decode($_COOKIE['logs']) : (object)[];
        $data = Log::search($search)->paginate(400);
        $data->getCollection()->map(function ($log) {
            if (in_array    ($log->table, ['attachments', 'archives', 'clients', 'request_lists', 'tutors']) && $log->type != 'delete') {
                switch ($log->table) {
                    case 'attachments':
                        if ($attachment = Models\Attachment::find($log->row_id)) {
                            $log->link = Models\Attachment::without(['archive', 'review'])->find($log->row_id)->link;
                        }
                        break;
                    case 'archives':
                        if ($archive = Models\Archive::find($log->row_id)) {
                            $log->link = Models\Attachment::without(['archive', 'review'])->find($archive->attachment_id)->link;
                        }
                        break;
                    case 'clients':
                        if (Models\Client::find($log->row_id)) {
                            $log->link = 'client/' . $log->row_id;
                        }
                        break;
                    case 'request_lists':
                        if ($rl = Models\RequestList::without(['attachments'])->find($log->row_id)) {
                            $log->link = 'requests/' . $rl->request_id . '/edit#' . $log->row_id;
                        }
                        break;
                    case 'requests':
                        if (Models\Request::find($log->row_id)) {
                            $log->link = 'requests/' . $log->row_id . '/edit';
                        }
                        break;
                    case 'tutors':
                        $log->link = 'tutors/' . $log->row_id . '/edit';
                        break;
                }
            }
            return $log;
        });

        return [
            'counts' => Log::counts($search),
            'data'   => $data,
        ];
        // return Log::search($search)->paginate(30);
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
