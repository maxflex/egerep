<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class StreamController extends Controller
{
    public function index(Request $request)
    {
        if (! allowed(\Shared\Rights::ER_STREAM)) {
            return view('errors.not_allowed');
        }
        return view('stream.index')->with(
            ngInit([
                'page'     => $request->page,
                'sort'     => dbFactory('sort')->get(),
                'places'   => dbFactory('places')->get(),
                'stations' => dbFactory('stations')->get(),
            ])
        );
    }
}
