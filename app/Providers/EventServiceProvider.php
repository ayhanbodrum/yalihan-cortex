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
        \App\Events\IlanCreated::class => [
            \App\Listeners\FindMatchingDemands::class, // Context7: Tersine Eşleştirme (Reverse Matching)
        ],
        \App\Events\TalepReceived::class => [
            \App\Jobs\AnalyzeAndPrioritizeDemand::class, // Context7: Otonom Fırsat Sentezi ve Bildirim Sistemi
        ],
        \App\Events\IlanPriceChanged::class => [
            \App\Listeners\NotifyN8nOnIlanPriceChanged::class, // Context7: Otonom Fiyat Değişim Takibi ve n8n Entegrasyonu
        ],
        // Context7: Takım Yönetimi Otomasyonu - Temel Event Sistemi
        \App\Events\GorevCreated::class => [
            \App\Listeners\NotifyN8nOnGorevCreated::class,
        ],
        \App\Events\GorevStatusChanged::class => [
            \App\Listeners\NotifyN8nOnGorevStatusChanged::class,
        ],
        \App\Events\GorevDeadlineYaklasiyor::class => [
            \App\Listeners\NotifyN8nOnGorevDeadlineYaklasiyor::class,
        ],
        \App\Events\GorevGecikti::class => [
            \App\Listeners\NotifyN8nOnGorevGecikti::class,
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
