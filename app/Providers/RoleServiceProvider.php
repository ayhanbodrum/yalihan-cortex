<?php

namespace App\Providers;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class RoleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // @role('roleName') or @role('role1', 'role2')
        Blade::directive('role', function ($roles) {
            return "<?php if(\Illuminate\Support\Facades\Auth::check() && \App\Providers\RoleServiceProvider::hasRole({$roles})): ?>";
        });

        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
        });

        // @admin
        Blade::if('admin', function () {
            return Auth::check() && Auth::user()->role->name === UserRole::SUPERADMIN->value;
        });

        // @danisman
        Blade::if('danisman', function () {
            return Auth::check() && Auth::user()->role->name === UserRole::DANISMAN->value;
        });

        // @editor
        Blade::if('editor', function () {
            return Auth::check() && Auth::user()->role->name === UserRole::EDITOR->value;
        });
    }

    /**
     * Kullanıcının belirtilen rollerden birine sahip olup olmadığını kontrol eder.
     *
     * @param  string|array  ...$roles
     */
    public static function hasRole(...$roles): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $user = Auth::user();
        $userRole = $user->role->name ?? null;

        if (! $userRole) {
            return false;
        }

        foreach ($roles as $role) {
            if ($userRole === $role) {
                return true;
            }
        }

        return false;
    }
}
