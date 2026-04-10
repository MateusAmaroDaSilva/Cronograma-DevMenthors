<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        // Mark as AJAX request if JSON or AJAX header is present
        if ($request->header('X-Requested-With') === 'XMLHttpRequest' || 
            $request->header('Content-Type') === 'application/json') {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
