<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Metro;

class MetroController extends Controller
{
    public function postClosest(Request $request)
    {
        return Metro::getClosest($request->lat, $request->lng);
    }
}
