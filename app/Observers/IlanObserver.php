<?php

namespace App\Observers;

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
            IlanPriceHistory::create([
                'ilan_id' => $ilan->id,
                'old_price' => $ilan->getOriginal('fiyat'),
                'new_price' => $ilan->fiyat,
                'currency' => $ilan->para_birimi ?? 'TRY',
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
        }
    }

    /**
     * Handle the Ilan "created" event.
     */
    public function created(Ilan $ilan): void
    {
        //
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
