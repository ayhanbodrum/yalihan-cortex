<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     * ✅ Context7: Telescope service provider uyumlu hale getirildi
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        // ✅ Context7: Telescope filter - Context7 uyumlu
        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            // Local environment'te tüm entry'leri kaydet
            if ($isLocal) {
                return true;
            }

            // Production'da sadece önemli entry'leri kaydet
            return $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     * ✅ Context7: Hassas verileri gizleme - Context7 uyumlu
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        // ✅ Context7: Hassas request parametrelerini gizle
        Telescope::hideRequestParameters([
            '_token',
            'password',
            'password_confirmation',
            'api_token',
            'secret',
        ]);

        // ✅ Context7: Hassas header'ları gizle
        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
            'authorization',
            'api-key',
        ]);
    }

    /**
     * Register the Telescope gate.
     * ✅ Context7: Telescope erişim kontrolü - Context7 uyumlu
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user = null) {
            // ✅ Context7: Local environment'te herkes erişebilir
            if ($this->app->environment('local')) {
                return true;
            }

            // ✅ Context7: Production'da sadece authenticated kullanıcılar
            if (! $user) {
                return false;
            }

            // ✅ Context7: Super admin veya admin rolü kontrolü (Spatie Permission)
            return $user->hasRole(['superadmin', 'admin']) ||
                   $user->email === config('app.admin_email', 'admin@example.com');
        });
    }
}
