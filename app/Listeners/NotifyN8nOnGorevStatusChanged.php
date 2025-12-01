<?php

namespace App\Listeners;

use App\Events\GorevStatusChanged;
use App\Jobs\NotifyN8nAboutGorevStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Görev Durumu Değişti Event Listener
 *
 * Context7: Takım Yönetimi Otomasyonu - Temel Event Sistemi
 */
class NotifyN8nOnGorevStatusChanged implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(GorevStatusChanged $event): void
    {
        $notificationChannels = ['telegram', 'whatsapp', 'email'];

        NotifyN8nAboutGorevStatusChanged::dispatch(
            $event->gorev->id,
            $event->oldStatus,
            $event->newStatus,
            $notificationChannels
        );
    }
}
