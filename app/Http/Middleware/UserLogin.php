<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\DelayedJob;
use App\Models\Service\Log;

class UserLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! User::loggedIn()) {
            if (! isset($_COOKIE['wallpapper_id'])) {
                $wallpapper_count = count(glob(getcwd() . '/img/wallpapper/*.jpg'));
                $wallpapper_id = mt_rand(1, $wallpapper_count);
                setcookie('wallpapper_id', mt_rand(1, $wallpapper_id),  time() + 68400, '/'); // 19 часов
            } else {
                $wallpapper_id = $_COOKIE['wallpapper_id'];
            }
            return view('login.login', compact('wallpapper_id'));
        }

        // иначе юзер залогинен
        DelayedJob::dispatch(
            \App\Jobs\Delayed\LogoutNotifyJob::class,
            ['user_id' => User::fromSession()->id],
            User::ADMIN_SESSION_DURATION - 1
        );

        // создать отложенную задачу на логаут
        DelayedJob::dispatch(
            \App\Jobs\Delayed\LogoutJob::class,
            ['session_id' => session_id()],
            User::ADMIN_SESSION_DURATION
        );

        view()->share('user', User::fromSession());

        return $next($request);
    }
}
