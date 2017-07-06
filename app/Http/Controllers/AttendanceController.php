<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    public function index()
    {
        if (! allowed(\Shared\Rights::ER_ATTENDANCE)) {
            return view('errors.not_allowed');
        }
        return view('attendance.index')->with(
            ngInit([
                'month' => date('n'),
            ])
        );
    }
}
