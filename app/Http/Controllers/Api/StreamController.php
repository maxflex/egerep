<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Stream;

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
        return [
            'data'   => Stream::search($search)->paginate(50),
            // 'counts' => Stream::counts($search)
        ];
    }
}
