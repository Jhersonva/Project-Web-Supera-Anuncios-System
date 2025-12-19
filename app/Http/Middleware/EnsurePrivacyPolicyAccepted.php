<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsurePrivacyPolicyAccepted
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->privacy_policy_accepted) {
            return redirect()->route('privacy-policy.show');
        }

        return $next($request);
    }
}
