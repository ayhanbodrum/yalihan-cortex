<?php

namespace Database\Seeders;

use App\Models\Il;
use App\Models\Ilan;
use App\Models\IlanKategori;
use App\Models\Ilce;
use App\Models\Mahalle;
use App\Models\Season;
use Illuminate\Database\Seeder;

class SampleYalikavakMarinaVillaSeeder extends Seeder
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
            'baslik' => 'Yalıkavak Marina Panoramik 4+1 Villa',
            'aciklama' => 'Yalıkavak Marina yakınında panoramik deniz manzaralı, özel havuzlu 4+1 villa. Modern iç mekan, geniş teras ve konforlu yaşam alanları.',
            'fiyat' => 12000,
            'para_birimi' => 'TRY',
            'status' => 1,
            'crm_only' => false,
            'il_id' => $il->id ?? null,
            'ilce_id' => $ilce->id ?? null,
            'mahalle_id' => $mahalle->id ?? null,
            'ana_kategori_id' => $yazlikKategori->id ?? null,
            'oda_sayisi' => 4,
            'banyo_sayisi' => 4,
            'salon_sayisi' => 1,
            'net_m2' => 280,
            'brut_m2' => 420,
            'havuz' => true,
            'havuz_turu' => 'özel',
            'min_konaklama' => 3,
            'max_misafir' => 8,
            'temizlik_ucreti' => 800,
            'adres' => 'Yalıkavak Marina, Bodrum/Muğla',
            'gunluk_fiyat' => 12000,
            'haftalik_fiyat' => 80000,
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
            'gunluk_fiyat' => 12000,
            'haftalik_fiyat' => 80000,
            'minimum_konaklama' => 3,
            'maksimum_konaklama' => 30,
            'status' => true,
        ]);
    }
}
