<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Service\SessionService;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        $key = base64_decode($request->key);
        list($time, $userId) = explode('|', $key);
        if ($time == date('Y-m-d H:i') && User::whereId($userId)->exists()) {
            $user = User::find($userId);
            if (! $user->isBanned()) {
                $user->toSession();
                SessionService::clearCache();
                return redirect($request->redirect);
            }
        }
        return redirect(config('sso.server') . 'login?url=' . url()->current() . '&access_denied');
    }

    /** пустой экшн, просто чтобы отработал MiddleWare – UserLogin */
    public function continueSession() {}
}
