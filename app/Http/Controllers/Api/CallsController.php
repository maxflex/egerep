<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class CallsController extends Controller
{
    public function index(Request $request)
    {
        // $search = isset($_COOKIE['logs']) ? json_decode($_COOKIE['logs']) : (object)[];
        // $data = Log::search($search)->paginate(30);

        $query = dbEgecrm('mango')->orderBy('mango.id', 'desc');

        if ($request->type) {
            if ($request->type == 1) {
                $query->where('from_extension', 0);
            }
            if ($request->type == 2) {
                $query->where('to_extension', 0);
            }
        }

        if ($request->status_1) {
            $query->leftJoin('call_statuses as cs1', function($join) use ($request) {
                $join->on('cs1.id', '=', 'mango.id')->on('cs1.status', '=', 1);
            });
            if ($request->status_1 == 1) {
                $query->whereRaw("cs1.id IS NOT NULL");
            } else {
                $query->whereRaw("cs1.id IS NULL");
            }
        }
        // if ($request->status_2) {
        //     $query->whereRaw(($request->status_2 == 2 ? "NOT" : "") . " exists(select 1 from call_statuses cs where cs.id=mango.id and cs.status=2 limit 1)");
        // }
        // if ($request->status_3) {
        //     $query->whereRaw(($request->status_3 == 2 ? "NOT" : "") . " exists(select 1 from call_statuses cs where cs.id=mango.id and cs.status=3 limit 1)");
        // }

        if ($request->user_id) {
            $query->whereRaw("(from_extension={$request->user_id} or to_extension={$request->user_id})");
        }

        if ($request->line_number) {
            $query->where('line_number', $request->line_number);
        }

        $data = $query->paginate(30);

        $data->getCollection()->map(function ($d) {
            $d->statuses = array_filter([$d->status_1, $d->status_2, $d->status_3]);
        });
        // foreach($data as &$d) {
        //     $d->statuses = dbEgecrm('call_statuses')->whereId($d->id)->get();
        // }

        return [
            'data' => $data,
        ];
    }
}
