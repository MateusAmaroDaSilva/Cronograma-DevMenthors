<?php

namespace App\Http\Middleware;

class RedirectIfAuthenticated
{
    public function handle($request, \Closure $next, ...$guards)
    {
        return $next($request);
    }
}
