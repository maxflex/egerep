<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models;
use App\Http\Controllers\Controller;
use App\Models\Service\Log;
use DB;

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
        $data = Log::search($search)->paginate(30);
        $data->getCollection()->map(function ($log) {
            if (in_array($log->table, ['attachments', 'archives', 'clients', 'request_lists', 'tutors']) && $log->type != 'delete') {
                switch ($log->table) {
                    case 'attachments':
                        $log->link = 'attachment/' . $log->row_id;
                        break;
                    case 'archives':
                        $log->link = 'archive/' . $log->row_id;
                        break;
                    case 'clients':
                        $log->link = 'client/' . $log->row_id;
                        break;
                    case 'request_lists':
                        $log->link = 'request-list/' . $log->row_id;
                        break;
                    case 'requests':
                        $log->link = 'requests/' . $log->row_id . '/edit';
                        break;
                    case 'tutors':
                        $log->link = 'tutors/' . $log->row_id . '/edit';
                        break;
                }
            }
            return $log;
        });

        return [
            // 'counts' => Log::counts($search),
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

    public function graph()
    {
        $search = isset($_COOKIE['logs']) ? json_decode($_COOKIE['logs']) : (object)[];
        // ->groupBy(DB::raw("DATE_FORMAT(`created_at`, '%H:%i')"))
        $data = Log::search($search)->orderBy('created_at', 'asc')->select('created_at')->pluck('created_at');

        // $return = [];
        // foreach($data as $d) {
        //     $return[] = [
        //         'date'  => $d,
        //         'value' => 1,
        //     ];
        // }

        return [
            'labels'    => $data,
            'datasets'  => array_fill(0, count($data), 1)
        ];
    }
}
