<?php

namespace App\Providers;

use App\Traits\AdminMenu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AdminMenuServiceProvider extends ServiceProvider
{
    use AdminMenu;

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $request = request();
            if ($request && $request->is('admin*')) {
                $view->with('adminMenu', $this->adminMenu());
            }
        });
    }
}
