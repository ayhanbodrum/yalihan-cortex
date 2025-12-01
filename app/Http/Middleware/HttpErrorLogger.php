<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HttpErrorLogger
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $status = $response->getStatusCode();
        if ($status >= 400) {
            Log::channel('security')->warning('http-error', [
                'status' => $status,
                'method' => $request->getMethod(),
                'path' => $request->getPathInfo(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);
        }

        return $response;
    }
}
