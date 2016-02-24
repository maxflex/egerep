<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Api;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ExternalController extends Controller
{
    public function exec($function, Request $request)
    {
        return Api::exec($function, $request->input());
    }
}
