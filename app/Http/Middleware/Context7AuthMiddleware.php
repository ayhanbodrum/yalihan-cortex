<?php

namespace App\Http\Middleware;

use App\Services\Context7AuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Context7 Auth Middleware
 *
 * Context7 standartlarına uygun kimlik doğrulama ve yetki kontrolü.
 */
class Context7AuthMiddleware
{
    protected $authService;

    public function __construct(Context7AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        // Kullanıcı giriş yapmamışsa login sayfasına yönlendir
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Bu sayfaya erişmek için giriş yapmanız gerekiyor.');
        }

        $user = Auth::user();

        // Context7 standartlarına uygun kullanıcı bilgilerini çevir - Spatie Permission
        $context7User = (object) [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_name' => $user->roles->first()->name ?? 'user',
            'status' => $user->status ?? true, // Context7: 'status' instead of 'enabled'
            'is_verified' => $user->is_verified ?? false,
        ];

        // Debug log
        Log::info('Context7AuthMiddleware Debug', [
            'user' => $context7User->name,
            'role' => $context7User->role_name,
            'permission' => $permission,
            'route' => $request->route()->getName(),
            'hasPermission' => $permission ? $this->authService->hasPermission($context7User, $permission) : 'N/A',
            'checkAccess' => $this->authService->checkAccess($context7User, $request->route()->getName()),
        ]);

        // Yetki kontrolü
        if ($permission && ! $this->authService->hasPermission($context7User, $permission)) {
            Log::warning('Permission denied', [
                'user' => $context7User->name,
                'permission' => $permission,
            ]);

            return redirect()->route('admin.dashboard.index')
                ->with('error', 'Bu sayfaya erişim yetkiniz bulunmuyor.');
        }

        // Route bazlı erişim kontrolü
        $currentRoute = $request->route()->getName();
        if (! $this->authService->checkAccess($context7User, $currentRoute)) {
            Log::warning('Route access denied', [
                'user' => $context7User->name,
                'route' => $currentRoute,
            ]);

            return redirect()->route('admin.dashboard.index')
                ->with('error', 'Bu sayfaya erişim yetkiniz bulunmuyor.');
        }

        // Son aktiviteyi güncelle
        $this->authService->updateLastActivity($user->id);

        return $next($request);
    }
}
