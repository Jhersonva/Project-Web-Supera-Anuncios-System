<?php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Asegúrate que el admin tenga role_id = 1
        if (Auth::user()->role_id != 1) {
            return redirect()->route('home')->with('error', 'Acceso denegado.');
        }

        return $next($request);
    }
}
