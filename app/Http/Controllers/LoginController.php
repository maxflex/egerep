<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Service\Settings;
use ReCaptcha\ReCaptcha;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        /**
         * Нельзя входить в режиме эксренного выхода
         */
        if (Settings::get('emergency_exit') == 1) {
            return false;
        }
        /**
         * Проверка капчи
         */
        $recaptcha = new ReCaptcha(config('captcha.secret'));
        $resp = $recaptcha->verify($request->captcha, $_SERVER['REMOTE_ADDR']);
        if (! $resp->isSuccess()) {
            return $resp->getErrorCodes();
        }

        return response()->json(User::login($request));
    }

    public function logout()
    {
        User::logout();
        return redirect('/');
    }
}
