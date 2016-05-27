<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SummaryController extends Controller
{
    const PER_PAGE = 30;

    public function index(Request $request)
    {
        return view('summary.index', [
                        'item_cnt' => \App\Models\Request::daysFromFirstReqeust(),
                        'per_page' => self::PER_PAGE
                    ])->with(
                        ngInit([
                            'page' => $request->page
                        ])
                    );
    }
}
