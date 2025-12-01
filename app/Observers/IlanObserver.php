<?php

namespace App\Observers;

use App\Events\IlanCreated;
use App\Events\IlanPriceChanged;
use App\Models\Ilan;
use App\Models\IlanPriceHistory;
use Illuminate\Support\Facades\Auth;

class IlanObserver
{
    /**
     * Handle the Ilan "updating" event.
     */
    public function updating(Ilan $ilan): void
    {
        // Fiyat alanının değişip değişmediğini kontrol et
        if ($ilan->isDirty('fiyat')) {
            $oldPrice = $ilan->getOriginal('fiyat');
            $newPrice = $ilan->fiyat;
            $currency = $ilan->para_birimi ?? 'TRY';

            // Fiyat geçmişi kaydı oluştur
            IlanPriceHistory::create([
                'ilan_id' => $ilan->id,
                'old_price' => $oldPrice,
                'new_price' => $newPrice,
                'currency' => $currency,
                'changed_by' => Auth::id(),
                'change_reason' => 'manual_update',
                'additional_data' => [
                    'old_currency' => $ilan->getOriginal('para_birimi'),
                    'new_currency' => $ilan->para_birimi,
                    'original_price_field' => $ilan->fiyat_orijinal ?? null,
                    'try_cached' => $ilan->fiyat_try_cached ?? null,
                    'rate' => $ilan->kur_orani ?? null,
                    'rate_date' => $ilan->kur_tarihi ?? null,
                ],
                'created_at' => now(),
            ]);

            // Context7: Otonom Fiyat Değişim Takibi - Event fırlat
            // Bu event, n8n'e bildirim gönderecek ve multi-channel (Telegram, WhatsApp, Email) bildirimi tetikleyecek
            event(new IlanPriceChanged($ilan, $oldPrice, $newPrice, $currency));
        }
    }

    /**
     * Handle the Ilan "created" event.
     *
     * Context7: Tersine Eşleştirme (Reverse Matching) için event fire edilir
     */
    public function created(Ilan $ilan): void
    {
        // Sadece 'Aktif' status'lu ilanlar için event fire et
        if ($ilan->status === 'Aktif') {
            event(new IlanCreated($ilan));
        }
    }

    /**
     * Handle the Ilan "updated" event.
     */
    public function updated(Ilan $ilan): void
    {
        //
    }

    /**
     * Handle the Ilan "deleted" event.
     */
    public function deleted(Ilan $ilan): void
    {
        //
    }

    /**
     * Handle the Ilan "restored" event.
     */
    public function restored(Ilan $ilan): void
    {
        //
    }

    /**
     * Handle the Ilan "force deleted" event.
     */
    public function forceDeleted(Ilan $ilan): void
    {
        //
    }
}
