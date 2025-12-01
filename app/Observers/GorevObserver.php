<?php

namespace App\Observers;

use App\Events\GorevCreated;
use App\Events\GorevDeadlineYaklasiyor;
use App\Events\GorevGecikti;
use App\Events\GorevStatusChanged;
use App\Modules\TakimYonetimi\Models\Gorev;

/**
 * Görev Observer
 *
 * Context7: Takım Yönetimi Otomasyonu - Temel Event Sistemi
 * Görev oluşturulduğunda, durumu değiştiğinde ve deadline yaklaştığında event'leri fırlatır.
 */
class GorevObserver
{
    /**
     * Handle the Gorev "created" event.
     *
     * Context7: Yeni görev oluşturulduğunda event fırlat
     */
    public function created(Gorev $gorev): void
    {
        // GorevCreated event'ini fırlat
        event(new GorevCreated($gorev));
    }

    /**
     * Handle the Gorev "updated" event.
     *
     * Context7: Görev güncellendiğinde durum ve deadline kontrolleri yap
     */
    public function updated(Gorev $gorev): void
    {
        // Status değişikliği kontrolü
        if ($gorev->isDirty('status')) {
            $oldStatus = $gorev->getOriginal('status');
            $newStatus = $gorev->status;

            // GorevStatusChanged event'ini fırlat
            event(new GorevStatusChanged($gorev, $oldStatus, $newStatus));
        }

        // Deadline yaklaşıyor mu kontrol et (bitis_tarihi değişmişse veya status değiştiğinde)
        if (($gorev->isDirty('bitis_tarihi') || $gorev->isDirty('status')) && $gorev->bitis_tarihi) {
            if ($gorev->status !== 'tamamlandi' && $gorev->status !== 'iptal') {
                // Deadline 1 gün içinde mi?
                if ($gorev->deadlineYaklasiyorMu(1)) {
                    $kalanGun = now()->diffInDays($gorev->bitis_tarihi, false);
                    if ($kalanGun >= 0 && $kalanGun <= 1) {
                        event(new GorevDeadlineYaklasiyor($gorev, (int) $kalanGun));
                    }
                }

                // Gecikti mi kontrol et
                if ($gorev->geciktiMi()) {
                    $gecikmeGunu = abs(now()->diffInDays($gorev->bitis_tarihi, false));
                    event(new GorevGecikti($gorev, (int) $gecikmeGunu));
                }
            }
        }
    }

    /**
     * Handle the Gorev "deleted" event.
     */
    public function deleted(Gorev $gorev): void
    {
        //
    }

    /**
     * Handle the Gorev "restored" event.
     */
    public function restored(Gorev $gorev): void
    {
        //
    }

    /**
     * Handle the Gorev "force deleted" event.
     */
    public function forceDeleted(Gorev $gorev): void
    {
        //
    }
}
