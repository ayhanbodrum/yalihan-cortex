<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Feature;
use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;
use App\Models\OzellikKategori;
use App\Models\User;
use App\Policies\FeaturePolicy;
use App\Policies\IlanKategoriPolicy;
use App\Policies\IlanKategoriYayinTipiPolicy;
use App\Policies\OzellikKategoriPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Feature::class => FeaturePolicy::class,
        OzellikKategori::class => OzellikKategoriPolicy::class,
        IlanKategori::class => IlanKategoriPolicy::class,
        IlanKategoriYayinTipi::class => IlanKategoriYayinTipiPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerBladeDirectives();

        // Superadmin otomatik olarak tüm izinlere sahiptir
        Gate::before(function (User $user) {
            if ($user->role && $user->role->name === UserRole::SUPERADMIN->value) {
                return true;
            }
        });

        // Rol bazlı Gate tanımları
        Gate::define('view-admin-panel', function (User $user) {
            return $user->role && in_array($user->role->name, [
                UserRole::SUPERADMIN->value,
                UserRole::DANISMAN->value,
                UserRole::EDITOR->value,
            ]);
        });

        Gate::define('manage-users', function (User $user) {
            return $user->role && $user->role->name === UserRole::SUPERADMIN->value;
        });

        Gate::define('manage-settings', function (User $user) {
            return $user->role && $user->role->name === UserRole::SUPERADMIN->value;
        });

        Gate::define('manage-ilanlar', function (User $user) {
            return $user->role && in_array($user->role->name, [
                UserRole::SUPERADMIN->value,
                UserRole::DANISMAN->value,
            ]);
        });

        Gate::define('edit-ilanlar', function (User $user) {
            return $user->role && in_array($user->role->name, [
                UserRole::SUPERADMIN->value,
                UserRole::DANISMAN->value,
                UserRole::EDITOR->value,
            ]);
        });
    }

    /**
     * Özel Blade direktiflerini kaydet
     */
    private function registerBladeDirectives(): void
    {
        // @role('roleName') or @role('role1', 'role2')
        Blade::directive('role', function ($roles) {
            return "<?php if(Auth::check() && \App\Providers\AuthServiceProvider::hasRole({$roles})): ?>";
        });

        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
        });

        // @admin
        Blade::if('admin', function () {
            return Auth::check() && Auth::user()->role && Auth::user()->role->name === UserRole::SUPERADMIN->value;
        });

        // @danisman
        Blade::if('danisman', function () {
            return Auth::check() && Auth::user()->role && Auth::user()->role->name === UserRole::DANISMAN->value;
        });

        // @editor
        Blade::if('editor', function () {
            return Auth::check() && Auth::user()->role && Auth::user()->role->name === UserRole::EDITOR->value;
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

        // Role ilişkisini kontrol et
        if ($user->role && isset($user->role->name)) {
            $userRole = $user->role->name;
        }
        // Doğrudan role_id özelliğini kontrol et
        elseif (isset($user->role_id)) {
            $userRole = $user->role_id;
        } else {
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
