<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Events\EmergencyExit;
use App\Models\User;

class EmergencyController extends Controller
{
    public function index()
    {
        if (! allowed(\Shared\Rights::EMERGENCY_EXIT)) {
            return view('errors.not_allowed');
        }

        event(new EmergencyExit);
        User::logout();
        return redirect('https://google.ru/');
    }
}
