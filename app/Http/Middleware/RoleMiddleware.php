<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Gelen isteği belirtilen rollere göre kontrol eder.
     *
     * Bu middleware, Spatie\Permission\Middleware\RoleMiddleware üzerine yapılandırılmıştır
     * ancak özel işlemler için ek fonksiyonlar eklenebilir.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|array  $role
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (! Auth::check()) {
            // Oturum açılmamışsa login sayfasına yönlendir.
            return redirect()->route('login');
        }

        $roles = is_array($role) ? $role : explode('|', $role);

        // Kullanıcı ve rollerini kontrol et
        if (! Auth::user()->hasAnyRole($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }

        return $next($request);
    }
}
