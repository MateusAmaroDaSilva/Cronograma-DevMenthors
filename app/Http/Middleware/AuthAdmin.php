<?php

namespace App\Http\Middleware;

class AuthAdmin
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}
