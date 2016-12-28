<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class GraphController extends Controller
{
    public function getIndex()
    {
        $stations   = \DB::table('stations')->get(['id', 'title', 'line_id']);
        $distances  = \DB::table('graph_distances')->get();

        return view('graph.index')->with(
            ngInit(compact('stations', 'distances'))
        );
    }

    public function postSave(Request $request)
    {
        $from   = min($request->from, $request->to);
        $to     = max($request->from, $request->to);
        $query  = \DB::table('graph_distances')->where('from', $from)->where('to', $to);

        if ($query->exists()) {
            $query->update([
                'distance' => $request->distance
            ]);
        } else {
            $query->insert($request->input());
        }
    }

    public function postDelete(Request $request)
    {
        $from   = min($request->from, $request->to);
        $to     = max($request->from, $request->to);
        \DB::table('graph_distances')->where('from', $from)->where('to', $to)->delete();
    }
}
