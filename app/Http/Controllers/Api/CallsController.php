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

        $query = DB::table('mango')->orderBy('id', 'desc');

        $data = $query->paginate(30);

        return [
            // 'counts' => Log::counts($search),
            'data'   => $data,
        ];
    }
}
