<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Service\Settings;

class EmergencyExit
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
        if (Settings::get('emergency_exit') == 1) {
            return response("<center><h1>500 Internal Server Error</h1><hr>nginx/1.4.6 (Ubuntu)</center>", 500);
        }
        return $next($request);
    }
}
