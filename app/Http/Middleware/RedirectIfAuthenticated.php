<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Redirect logged-in users trying to access login/register pages.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // If user is already logged in, send to profile
            return redirect()->route('accounts.profile');
        }

        return $next($request);
    }
}
