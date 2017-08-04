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

        if (! isBlank($request->status_1)) {
            $query->where('status_1', $request->status_1);
        }
        if (! isBlank($request->status_2)) {
            $query->where('status_2', $request->status_2);
        }
        if (! isBlank($request->status_3)) {
            $query->where('status_3', $request->status_3);
        }

        if ($request->user_id) {
            $query->whereRaw("(from_extension={$request->user_id} or to_extension={$request->user_id})");
        }

        if ($request->line_number) {
            $query->where('line_number', $request->line_number);
        }

        $data = $query->paginate(30);

        $data->getCollection()->map(function ($d) {
            $d->statuses = array_keys(array_filter([$d->status_1, $d->status_2, $d->status_3]));
        });

        return [
            'data' => $data,
        ];
    }
}
