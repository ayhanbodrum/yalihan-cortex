<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

/**
 * Base Admin Controller
 * Context7: Tüm admin controller'lar için ortak davranışlar
 *
 * Yalıhan Bekçi Fix: Undefined variable sorunlarını çözmek için
 * tüm admin sayfalarında ortak kullanılan değişkenler burada tanımlanır
 */
class AdminController extends Controller
{
    use \App\Traits\AdminMenu;

    /**
     * Constructor
     * Context7: Ortak değişkenleri tüm view'lara paylaş
     */
    public function __construct()
    {
        $this->middleware('auth');

        // Share common data to all admin views
        $this->shareCommonData();
    }

    /**
     * Share common data to all views
     * Context7: Tüm admin view'larda kullanılan ortak değişkenler
     *
     * Yalıhan Bekçi Çözüm:
     * - $status (İlan durumları)
     * - $taslak (Boolean filter)
     * - $etiketler (Active tags)
     * - $ulkeler (Countries)
     */
    protected function shareCommonData(): void
    {
        View::share([
            // Status options (İlan durumları)
            // Context7: status field kullanımı (aktif kelimesi sadece display text)
            'status' => [
                'draft' => 'Taslak',
                'pending' => 'Onay Bekliyor',
                'active' => 'Yayında', // Context7: "Aktif" yerine "Yayında" kullanıldı
                'sold' => 'Satıldı',
                'rented' => 'Kiralandı',
                'inactive' => 'Pasif',
                'archived' => 'Arşivlendi',
            ],

            // Taslak filter (Boolean)
            'taslak' => [
                '0' => 'Hayır',
                '1' => 'Evet',
            ],

            // Etiketler (Active tags only)
            'etiketler' => Cache::remember('admin.etiketler', 3600, function () {
                if (class_exists(\App\Models\Etiket::class)) {
                    return \App\Models\Etiket::where('status', true)
                        ->orderBy('id')
                        ->get();
                }

                return collect([]);
            }),

            // Ülkeler (Active countries only)
            'ulkeler' => Cache::remember('admin.ulkeler', 3600, function () {
                if (class_exists(\App\Models\Ulke::class)) {
                    return \App\Models\Ulke::orderBy('ulke_adi')
                        ->get();
                }

                return collect([]);
            }),

            // Ek ortak değişkenler
            'para_birimleri' => [
                'TRY' => '₺ TL',
                'USD' => '$ USD',
                'EUR' => '€ EUR',
                'GBP' => '£ GBP',
            ],

            'yayin_tipleri' => Cache::remember('admin.yayin_tipleri', 3600, function () {
                if (class_exists(\App\Models\IlanKategoriYayinTipi::class)) {
                    return \App\Models\IlanKategoriYayinTipi::where('status', true)
                        ->orderBy('display_order')
                        ->orderBy('yayin_tipi')
                        ->get();
                }

                return collect([]);
            }),
        ]);
    }

    /**
     * Clear shared data cache
     * Context7: Cache'i temizle (ayarlar değiştiğinde)
     */
    protected function clearSharedDataCache(): void
    {
        Cache::forget('admin.etiketler');
        Cache::forget('admin.ulkeler');
        Cache::forget('admin.yayin_tipleri');
    }
}
