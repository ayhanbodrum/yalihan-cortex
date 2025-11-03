<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // Kullanıcı aktivite takibi için eventler
        'App\Events\UserLoggedIn' => [
            'App\Listeners\LogUserActivity',
        ],
        'App\Events\IlanCreated' => [
            'App\Listeners\ProcessIlanCreation',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Performans modülü için kullanıcı aktivitelerini dinle
        Event::listen('user.activity', function ($user, $action) {
            // Kullanıcı aktivite logu oluştur
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
