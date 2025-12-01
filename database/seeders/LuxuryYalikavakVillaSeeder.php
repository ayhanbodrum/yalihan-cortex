<?php

namespace Database\Seeders;

use App\Models\Il;
use App\Models\Ilan;
use App\Models\IlanKategori;
use App\Models\IlanTakvimSync;
use App\Models\Ilce;
use App\Models\Mahalle;
use App\Models\Season;
use Illuminate\Database\Seeder;

class LuxuryYalikavakVillaSeeder extends Seeder
{
    public function run(): void
    {
        $il = Il::where('il_adi', 'Muğla')->first();
        $ilce = $il ? Ilce::where('il_id', $il->id)->where('ilce_adi', 'Bodrum')->first() : null;
        $mahalle = $ilce ? Mahalle::where('ilce_id', $ilce->id)->where('mahalle_adi', 'like', '%Yalıkavak%')->first() : null;

        $yazlikKategori = IlanKategori::where('slug', 'yazlik-kiralama')
            ->orWhere('slug', 'yazlik')
            ->first();

        $ilan = Ilan::create([
            'baslik' => 'Yalıkavak Ultra Lüks 5+1 Villa',
            'aciklama' => 'Bodrum Yalıkavak bölgesinde deniz manzaralı, özel havuzlu, 5 odalı ultra lüks villa. Modern mimari, geniş yaşam alanları ve üst düzey konfor.',
            'fiyat' => 15000,
            'para_birimi' => 'TRY',
            'status' => 1,
            'crm_only' => false,
            'il_id' => $il->id ?? null,
            'ilce_id' => $ilce->id ?? null,
            'mahalle_id' => $mahalle->id ?? null,
            'ana_kategori_id' => $yazlikKategori->id ?? null,
            'oda_sayisi' => 5,
            'banyo_sayisi' => 5,
            'salon_sayisi' => 2,
            'net_m2' => 350,
            'brut_m2' => 500,
            'havuz' => true,
            'havuz_turu' => 'özel',
            'min_konaklama' => 3,
            'max_misafir' => 10,
            'temizlik_ucreti' => 1000,
            'adres' => 'Yalıkavak, Bodrum/Muğla',
            'gunluk_fiyat' => 15000,
            'haftalik_fiyat' => 100000,
            'sezon_baslangic' => '2025-06-01',
            'sezon_bitis' => '2025-09-15',
            'elektrik_dahil' => false,
            'su_dahil' => false,
        ]);

        Season::create([
            'ilan_id' => $ilan->id,
            'sezon_tipi' => 'yaz',
            'baslangic_tarihi' => '2025-06-01',
            'bitis_tarihi' => '2025-09-15',
            'gunluk_fiyat' => 15000,
            'haftalik_fiyat' => 100000,
            'minimum_konaklama' => 3,
            'maksimum_konaklama' => 30,
            'status' => true,
        ]);

        IlanTakvimSync::create([
            'ilan_id' => $ilan->id,
            'platform' => 'airbnb',
            'external_listing_id' => '1390612252594322907',
            'ical_url' => 'https://www.airbnb.com.tr/calendar/ical/1390612252594322907.ics?s=2cc1862be98d3ab1520748919abb9173',
            'sync_enabled' => true,
            'auto_sync' => true,
            'sync_interval_minutes' => 60,
            'sync_status' => 'active',
        ]);
    }
}
