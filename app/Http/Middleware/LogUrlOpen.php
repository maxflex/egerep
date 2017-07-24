<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Service\Log;

class LogUrlOpen
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
        // логируем проход по URL
        if (User::loggedIn()) {
            Log::custom('url', User::fromSession()->id, ['url' => @$_SERVER['REQUEST_URI']]);
        }
        return $next($request);
    }
}
