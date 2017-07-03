<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Service\Settings;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if (Settings::get('emergency_exit') == 1) {
            return false;
        }
        return response()->json(User::login($request));
    }

    public function logout()
    {
        User::logout();
        return redirect('/');
    }
}
