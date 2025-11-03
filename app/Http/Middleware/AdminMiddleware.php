<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Spatie Permission ile rol kontrolü
        $allowedRoles = ['superadmin', 'admin', 'danisman'];
        $hasAllowedRole = false;
        
        foreach ($allowedRoles as $role) {
            if ($user->hasRole($role)) {
                $hasAllowedRole = true;
                break;
            }
        }
        
        if (!$hasAllowedRole) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        return $next($request);
    }
}
