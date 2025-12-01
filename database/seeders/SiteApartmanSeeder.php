<?php

namespace Database\Seeders;

use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use App\Models\SiteApartman;
use Illuminate\Database\Seeder;

class SiteApartmanSeeder extends Seeder
{
    /**
     * Context7: Yalıkavak bölgesi site/apartmanları
     */
    public function run(): void
    {
        // Muğla ili
        $mugla = Il::where('il_adi', 'Muğla')->first();
        $bodrum = $mugla ? Ilce::where('il_id', $mugla->id)->where('ilce_adi', 'Bodrum')->first() : null;
        $yalikavak = $bodrum ? Mahalle::where('ilce_id', $bodrum->id)->where('mahalle_adi', 'Yalıkavak')->first() : null;

        $sites = [
            [
                'name' => 'Cennet Evleri',
                'adres' => 'Yalıkavak, Bodrum, Muğla',
                'il_id' => $mugla?->id,
                'ilce_id' => $bodrum?->id,
                'mahalle_id' => $yalikavak?->id,
                'yonetici_adi' => null,
                'yonetici_telefon' => null,
                'toplam_daire_sayisi' => 24,
                'kat_sayisi' => 4,
                'asansor_sayisi' => 2,
                'otopark_statusu' => 'Kapalı Otopark',
                'sosyal_tesisler' => json_encode(['Yüzme Havuzu', 'Fitness Center', 'Çocuk Oyun Alanı']),
                'guvenlik_sistemi' => json_encode(['7/24 Güvenlik', 'Kamera Sistemi']),
                'yapim_yili' => 2018,
                'notlar' => 'Yalıkavak merkezde, denize yakın lüks site',
            ],
            [
                'name' => 'Ülkerler Sitesi',
                'adres' => 'Yalıkavak, Bodrum, Muğla',
                'il_id' => $mugla?->id,
                'ilce_id' => $bodrum?->id,
                'mahalle_id' => $yalikavak?->id,
                'toplam_daire_sayisi' => 36,
                'kat_sayisi' => 6,
                'asansor_sayisi' => 3,
                'otopark_statusu' => 'Kapalı Otopark',
                'sosyal_tesisler' => json_encode(['Yüzme Havuzu', 'Sosyal Tesis', 'Spor Alanları']),
                'guvenlik_sistemi' => json_encode(['7/24 Güvenlik', 'Kamera Sistemi', 'Kartlı Giriş']),
                'yapim_yili' => 2015,
                'notlar' => 'Eski ve köklü site, Yalıkavak\'ta tanınır',
            ],
            [
                'name' => 'Yalıhan Evleri',
                'adres' => 'Yalıkavak, Bodrum, Muğla',
                'il_id' => $mugla?->id,
                'ilce_id' => $bodrum?->id,
                'mahalle_id' => $yalikavak?->id,
                'toplam_daire_sayisi' => 18,
                'kat_sayisi' => 3,
                'asansor_sayisi' => 2,
                'otopark_statusu' => 'Açık + Kapalı Otopark',
                'sosyal_tesisler' => json_encode(['Yüzme Havuzu', 'Çocuk Oyun Alanı', 'Bahçe']),
                'guvenlik_sistemi' => json_encode(['7/24 Güvenlik', 'Kamera Sistemi']),
                'yapim_yili' => 2020,
                'notlar' => 'Modern ve lüks site, deniz manzaralı',
            ],
            [
                'name' => 'Gürnaz Evleri',
                'adres' => 'Yalıkavak, Bodrum, Muğla',
                'il_id' => $mugla?->id,
                'ilce_id' => $bodrum?->id,
                'mahalle_id' => $yalikavak?->id,
                'toplam_daire_sayisi' => 12,
                'kat_sayisi' => 2,
                'asansor_sayisi' => 1,
                'otopark_statusu' => 'Kapalı Otopark',
                'sosyal_tesisler' => json_encode(['Yüzme Havuzu', 'Bahçe']),
                'guvenlik_sistemi' => json_encode(['Kamera Sistemi']),
                'yapim_yili' => 2019,
                'notlar' => 'Butik site, sakin konum',
            ],
        ];

        foreach ($sites as $siteData) {
            SiteApartman::updateOrCreate(
                ['name' => $siteData['name']],
                $siteData
            );
        }

        $this->command->info('✅ Site/Apartman verileri eklendi!');
        $this->command->info('   Toplam: '.SiteApartman::count().' site');
    }
}
