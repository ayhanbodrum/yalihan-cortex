<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Address module removed - using unified location selector instead
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Emlak modülü için view namespace'i kaydet - eğer dizin varsa
        if (is_dir(resource_path('views/emlak'))) {
            $this->loadViewsFrom(resource_path('views/emlak'), 'emlak-views');
        }

        // ModuleServiceProvider'da auth ve crm view yollarını devre dışı bırakıyoruz
        // Çünkü bu namespace'ler her modülün kendi ServiceProvider'ında tanımlanmış
        // $this->loadViewsFrom(resource_path('views/auth'), 'auth');
        // $this->loadViewsFrom(resource_path('views/crm'), 'crm');

        // Admin modülü için view namespace'i kaydet
        if (is_dir(base_path('app/Modules/Admin/Views'))) {
            $this->loadViewsFrom(base_path('app/Modules/Admin/Views'), 'admin');
        }
    }
}
