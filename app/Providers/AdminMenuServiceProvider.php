<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Traits\AdminMenu;

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