<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Stream;
use DB;
use DateTime;

class StreamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search = isset($_COOKIE['stream']) ? json_decode($_COOKIE['stream']) : (object)[];

        $data = Stream::search($search)->paginate(50);

        $data->getCollection()->map(function ($d) {
            # для событий типа request найти соответствующую заявку в таблице requests
            # соответствие найти по времени +10/-10 сек
            if ($d->action == 'request') {
                $date = new DateTime($d->created_at);
                $interval_from = cloneQuery($date)->modify('-10 seconds')->format('Y-m-d H:i:s');
                $interval_to   = cloneQuery($date)->modify('+10 seconds')->format('Y-m-d H:i:s');
                $d->request_ids = DB::table('requests')
                    ->where('created_at', '>=', $interval_from)
                    ->where('created_at', '<=', $interval_to)
                    ->pluck('id');
            }
        });

        return compact('data');
    }
}
