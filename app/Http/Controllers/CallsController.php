<?php

namespace App\Http\Controllers;

use App\Models\Service\Call;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CallsController extends Controller
{
    public function index()
    {
        return view('calls.index')->with(
            ngInit([
                // 'month' => date('n'),
            ])
        );
    }

    public function missed()
    {
        return view('calls.missed')->with(ngInit([
            'calls'     => Call::missed()
        ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($entry_id)
    {
        Call::excludeFromMissed($entry_id);
    }
}
