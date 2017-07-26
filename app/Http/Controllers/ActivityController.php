<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ActivityController extends Controller
{
    public function index()
    {
        if (! allowed(\Shared\Rights::ER_ACTIVITY)) {
            return view('errors.not_allowed');
        }
        return view('activity.index');
    }
}
