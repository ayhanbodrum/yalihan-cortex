<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  mixed  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Rolleri işle - boru karakteri ile ayrılmış rolleri ayrı diziye çevir
        $roleArray = [];
        foreach ($roles as $role) {
            $roleArray = array_merge($roleArray, explode('|', $role));
        }

        // Kullanıcının rolünü kontrol et
        if ($user->role && in_array($user->role->name, $roleArray)) {
            return $next($request);
        }

        abort(403, 'Yetkiniz yok.');
    }
}
