<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $csp = "default-src 'self'; "
            . "img-src 'self' data:; "
            . "script-src 'self'; "
            . "style-src 'self' 'unsafe-inline'; "
            . "connect-src 'self'; "
            . "frame-ancestors 'self'; "
            . "base-uri 'self'; "
            . "form-action 'self'";

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('Permissions-Policy', "geolocation=(), camera=(), microphone=()");

        return $response;
    }
}