<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdvertiserMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Solo permite advertising_user (role_id = 2)
        if (Auth::user()->role_id != 2) {
            return redirect()->route('home')->with('error', 'Acceso denegado.');
        }

        return $next($request);
    }
}
