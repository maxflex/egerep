<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\DelayedJob;
use App\Models\Service\Log;
use App\Models\Background;

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
            $wallpaper = Background::approved()->where('date', now(true))->first();
            if ($wallpaper === null) {
                $wallpaper = Background::approved()->where('date', '<', now(true))->orderBy('id', 'desc')->first();
                // если не найден, делаем dummy-объект с зеленым фоном
                if ($wallpaper === null) {
                    $wallpaper = (object)[
                        'image_url' => 'img/background/green.png'
                    ];
                }
            }
            return view('login.login', compact('wallpaper'));
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
