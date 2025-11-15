<?php

namespace App\Services;

class MenuService
{
    /**
     * Basic MenuService implementation
     */
    public function getMenuItems()
    {
        return [];
    }

    public function generateMenu($userRole = null)
    {
        return [
            'dashboard' => ['name' => 'Dashboard', 'route' => 'admin.dashboard.index'],
            'users' => ['name' => 'Kullanıcılar', 'route' => 'admin.kullanicilar.index'],
        ];
    }

    /**
     * Get menu items for specific role
     * Context7 compliant implementation - safe routes only
     */
    public function getMenuForRole($role)
    {
        // Base menu with safe routes
        $baseMenu = [
            [
                'name' => 'Dashboard',
                'url' => '#',  // Using # for now as route may not exist
                'icon' => 'fas fa-tachometer-alt',
                'active' => true
            ]
        ];

        switch ($role) {
            case 'superadmin':
                return array_merge($baseMenu, [
                    [
                        'name' => 'Kullanıcılar',
                        'route' => 'admin.kullanicilar.index',
                        'icon' => 'fas fa-users',
                        'active' => true
                    ]
                    // Removed non-existing routes to prevent errors
                ]);

            case 'admin':
                return array_merge($baseMenu, [
                    [
                        'name' => 'Kullanıcılar',
                        'route' => 'admin.kullanicilar.index',
                        'icon' => 'fas fa-users',
                        'active' => true
                    ]
                ]);

            case 'danisman':
                return array_merge($baseMenu, [
                    [
                        'name' => 'Kullanıcılar',
                        'route' => 'admin.kullanicilar.index',
                        'icon' => 'fas fa-users',
                        'active' => true
                    ]
                ]);

            case 'user':
            default:
                return $baseMenu;
        }
    }
}
