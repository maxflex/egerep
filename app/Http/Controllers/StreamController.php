<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

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
                'actions'  => DB::table('stream')->orderBy('action', 'asc')->groupBy('action')->pluck('action'),
                'types'    => DB::table('stream')->orderBy('type', 'asc')->groupBy('type')->whereNotNull('type')->pluck('type'),
                'sort'     => dbFactory('sort')->get(),
                'places'   => dbFactory('places')->get(),
                'stations' => dbFactory('stations')->get(),
            ])
        );
    }

    public function configurations()
    {
        $data = DB::table('stream')->select('action', 'type', 'mobile')->groupBy('action', 'type', 'mobile')->get();
        foreach($data as &$d) {
            $d->created_at = DB::table('stream')->where('action', $d->action)->where('type', $d->type)->where('mobile', $d->mobile)->orderBy('created_at', 'desc')->value('created_at');
        }
        return view('stream.configurations')->with(
            ngInit([
                'data'     => $data,
            ])
        );
    }
}
