<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etiket;
use App\Models\Ulke;
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
     * - $status (574 hata) ✅ FIXED
     * - $taslak (328 hata) ✅ FIXED
     * - $etiketler (164 hata) ✅ FIXED
     * - $ulkeler (164 hata) ✅ FIXED
     */
    protected function shareCommonData(): void
    {
        View::share([
            // Status options (İlan durumları)
            'status' => [
                'draft' => 'Taslak',
                'pending' => 'Onay Bekliyor',
                'active' => 'Aktif',
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
                return Etiket::where('status', true)
                    ->orderBy('id')
                    ->get();
            }),

            // Ülkeler (Active countries only)
            'ulkeler' => Cache::remember('admin.ulkeler', 3600, function () {
                return Ulke::where('status', 'Aktif')
                    ->orderBy('ulke_adi')
                    ->get();
            }),

            // Ek ortak değişkenler
            'para_birimleri' => [
                'TRY' => '₺ TL',
                'USD' => '$ USD',
                'EUR' => '€ EUR',
                'GBP' => '£ GBP',
            ],

            'yayin_tipleri' => Cache::remember('admin.yayin_tipleri', 3600, function () {
                return \App\Models\IlanKategoriYayinTipi::where('status', true)
                    ->orderBy('yayin_tipi')
                    ->get();
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

