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

        if ($request->status) {
            $query->join('call_statuses', 'call_statuses.id', '=', 'mango.id')->where('status', $request->status);
        }

        if ($request->user_id) {
            $query->whereRaw("(from_extension={$request->user_id} or to_extension={$request->user_id})");
        }

        $data = $query->paginate(30);

        $data->getCollection()->map(function ($d) {
            $d->statuses = dbEgecrm('call_statuses')->whereId($d->id)->get();
        });

        return [
            // 'counts' => Log::counts($search),
            'data'   => $data,
        ];
    }
}
