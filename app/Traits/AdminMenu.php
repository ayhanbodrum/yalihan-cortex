<?php

namespace App\Traits;

use App\Services\MenuService;

trait AdminMenu
{
    protected function adminMenu(?string $role = null): array
    {
        $service = app(MenuService::class);
        $roleName = $role ?: (auth()->check() ? (auth()->user()->hasRole('superadmin') ? 'superadmin' : (auth()->user()->hasRole('admin') ? 'admin' : 'user')) : 'user');
        return $service->getMenuForRole($roleName, auth()->id());
    }
}