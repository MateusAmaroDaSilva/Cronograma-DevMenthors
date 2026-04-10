<?php

namespace App\Http\Middleware;

class ValidateSignature
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}
