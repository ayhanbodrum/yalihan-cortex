<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Context7ComplianceMiddleware
{
    /**
     * Handle an incoming request and check Context7 compliance
     */
    public function handle(Request $request, Closure $next)
    {
        // Only run in development environment
        if (!app()->environment('local', 'development')) {
            return $next($request);
        }
        
        // Check if Context7 debugging is enabled
        if (!config('app.context7_debug', false)) {
            return $next($request);
        }
        
        // Perform lightweight compliance check on request data
        $this->checkRequestCompliance($request);
        
        $response = $next($request);
        
        // Add Context7 headers for debugging
        if ($response->headers->has('Content-Type') && 
            str_contains($response->headers->get('Content-Type'), 'text/html')) {
            $this->addContext7Headers($response);
        }
        
        return $response;
    }
    
    private function checkRequestCompliance(Request $request)
    {
        $violations = [];
        
        // Check request parameters for Context7 violations
        $allInput = $request->all();
        foreach ($allInput as $key => $value) {
            if (in_array($key, ['status', 'active', 'status', 'city'])) {
                $violations[] = "Request parameter '{$key}' violates Context7 standards";
            }
        }
        
        // Log violations in development
        if (!empty($violations)) {
            logger()->warning('Context7 violations in request', [
                'violations' => $violations,
                'url' => $request->url(),
                'method' => $request->method()
            ]);
        }
    }
    
    private function addContext7Headers($response)
    {
        $response->headers->set('X-Context7-Checked', 'true');
        $response->headers->set('X-Context7-Timestamp', now()->toISOString());
    }
}