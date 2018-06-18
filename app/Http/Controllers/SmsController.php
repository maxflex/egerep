<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SmsController extends Controller
{
    public function index()
    {
        return view('sms.index')->with(
            ngInit([
                // 'month' => date('n'),
            ])
        );
    }
}
