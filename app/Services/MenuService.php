<?php

namespace App\Services;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

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
    public function getMenuForRole($role, $userId = null)
    {
        $baseMenu = [
            [
                'type' => 'link',
                'name' => 'Dashboard',
                'route' => 'admin.dashboard.index',
                'icon' => 'dashboard'
            ],
            [
                'type' => 'group',
                'name' => 'İlan Yönetimi',
                'icon' => 'listing',
                'children' => [
                    ['type' => 'link', 'name' => 'Tüm İlanlar', 'route' => 'admin.ilanlar.index'],
                    ['type' => 'link', 'name' => 'Yeni İlan', 'route' => 'admin.ilanlar.create'],
                    ['type' => 'link', 'name' => 'İlan Kategorileri', 'route' => 'admin.ilan-kategorileri.index'],
                    ['type' => 'link', 'name' => 'Yayın Tipi Yöneticisi', 'route' => 'admin.property_types.index'],
                    ['type' => 'link', 'name' => 'Özellik Grupları', 'route' => 'admin.ozellikler.kategoriler.index'],
                    ['type' => 'link', 'name' => 'Özellikler', 'route' => 'admin.ozellikler.index'],
                ]
            ],
            [
                'type' => 'link',
                'name' => 'Kullanıcılar',
                'route' => 'admin.kullanicilar.index',
                'icon' => 'users'
            ],
            [
                'type' => 'group',
                'name' => 'CRM Yönetimi',
                'icon' => 'crm',
                'children' => [
                    ['type' => 'link', 'name' => 'CRM Dashboard', 'route' => 'admin.crm.dashboard'],
                    ['type' => 'link', 'name' => 'Kişiler', 'route' => 'admin.kisiler.index'],
                    ['type' => 'link', 'name' => 'Talepler', 'route' => 'admin.talepler.index'],
                    ['type' => 'link', 'name' => 'Eşleştirmeler', 'route' => 'admin.eslesmeler.index'],
                    ['type' => 'link', 'name' => 'Talep-Portföy', 'route' => 'admin.talep-portfolyo.index'],
                ]
            ],
            [
                'type' => 'group',
                'name' => 'AI Sistemi',
                'icon' => 'ai',
                'children' => [
                    ['type' => 'link', 'name' => 'AI Ayarları', 'route' => 'admin.ai-settings.index'],
                    ['type' => 'link', 'name' => 'AI Analytics', 'route' => 'admin.ai-settings.analytics'],
                    ['type' => 'link', 'name' => 'AI Monitoring', 'route' => 'admin.ai-monitor.index'],
                ]
            ],
            [
                'type' => 'group',
                'name' => 'Blog Yönetimi',
                'icon' => 'blog',
                'children' => [
                    ['type' => 'link', 'name' => 'Yazılar', 'route' => 'admin.blog.posts.index'],
                    ['type' => 'link', 'name' => 'Kategoriler', 'route' => 'admin.blog.categories.index'],
                    ['type' => 'link', 'name' => 'Yorumlar', 'route' => 'admin.blog.comments.index'],
                ]
            ],
            [
                'type' => 'group',
                'name' => 'Adres Yönetimi',
                'icon' => 'location',
                'children' => [
                    ['type' => 'link', 'name' => 'Adres Yönetimi', 'route' => 'admin.adres-yonetimi.index'],
                    ['type' => 'link', 'name' => 'Wikimapia Arama', 'route' => 'admin.wikimapia-search.index'],
                ]
            ],
            [
                'type' => 'link',
                'name' => 'Raporlar',
                'route' => 'admin.reports.index',
                'icon' => 'reports'
            ],
            [
                'type' => 'link',
                'name' => 'Bildirimler',
                'route' => 'admin.notifications.index',
                'icon' => 'notifications'
            ],
            [
                'type' => 'link',
                'name' => 'Ayarlar',
                'route' => 'admin.ayarlar.index',
                'icon' => 'settings'
            ],
        ];

        $cacheKey = 'admin_menu:'.($role ?? 'guest').':'.($userId ?? '0');
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($baseMenu, $role) {
            $allowed = Gate::allows('view-admin-panel');
            $filtered = [];
            foreach ($baseMenu as $item) {
                if (isset($item['route']) && !Route::has($item['route'])) {
                    continue;
                }
                if (!$allowed && ($item['route'] ?? null) !== 'admin.dashboard.index') {
                    continue;
                }
                if (isset($item['children'])) {
                    $children = [];
                    foreach ($item['children'] as $child) {
                        if (isset($child['route']) && !Route::has($child['route'])) {
                            continue;
                        }
                        if (!$allowed) {
                            continue;
                        }
                        $children[] = $child;
                    }
                    if (!empty($children)) {
                        $item['children'] = $children;
                        $filtered[] = $item;
                    }
                    continue;
                }
                $filtered[] = $item;
            }
            Log::channel('module_changes')->info('admin-menu-generated', [
                'role' => $role,
                'count' => count($filtered),
            ]);
            return $filtered;
        });
    }
}
