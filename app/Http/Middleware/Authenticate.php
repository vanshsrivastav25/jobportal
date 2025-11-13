<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Redirect users who are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        // Redirect guest users to the login page
        if (! $request->expectsJson()) {
            return route('accounts.login');
        }

        return null;
    }
}
