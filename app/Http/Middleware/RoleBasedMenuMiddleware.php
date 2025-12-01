<?php

namespace App\Http\Middleware;

use App\Services\MenuService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedMenuMiddleware
{
    protected $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('RoleBasedMenuMiddleware - ÇALIŞIYOR! URL: '.$request->url());

        if (auth()->check()) {
            $user = auth()->user();
            Log::info('RoleBasedMenuMiddleware - Kullanıcı giriş yapmış: '.$user->email);

            // Kullanıcının en yüksek rolünü belirle
            $roleName = 'user'; // Default

            if ($user->hasRole('superadmin')) {
                $roleName = 'superadmin';
            } elseif ($user->hasRole('admin')) {
                $roleName = 'admin';
            } elseif ($user->hasRole('danisman')) {
                $roleName = 'danisman';
            } elseif ($user->hasRole('editor')) {
                $roleName = 'danisman'; // Editor de danışman menüsünü görür
            }

            $menuItems = $this->menuService->getMenuForRole($roleName);
            Log::info('RoleBasedMenuMiddleware - MenuItems sayısı: '.count($menuItems));
            Log::info('RoleBasedMenuMiddleware - Role: '.$roleName);

            // View'a veri paylaş
            view()->share('menuItems', $menuItems);
            view()->share('userRole', $roleName);

            // Request'e de ekle
            $request->attributes->set('menuItems', $menuItems);
            $request->attributes->set('userRole', $roleName);
        } else {
            Log::info('RoleBasedMenuMiddleware - Kullanıcı giriş yapmamış, default menu');
            // Kullanıcı giriş yapmamışsa default menu
            $menuItems = $this->menuService->getMenuForRole('user');
            Log::info('RoleBasedMenuMiddleware - Default MenuItems sayısı: '.count($menuItems));

            // View'a veri paylaş
            view()->share('menuItems', $menuItems);
            view()->share('userRole', 'user');

            // Request'e de ekle
            $request->attributes->set('menuItems', $menuItems);
            $request->attributes->set('userRole', 'user');
        }

        return $next($request);
    }
}
