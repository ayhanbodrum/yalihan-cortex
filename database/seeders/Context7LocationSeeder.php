<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Context7 Location Seeder
 *
 * Context7 standartlarına uygun lokasyon verileri.
 * Tüm eski lokasyon seeder'larından verileri birleştirir.
 *
 * Context7 Standardı: C7-LOCATION-SEEDER-2025-09-13
 * Versiyon: 4.0.0
 */
class Context7LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🗺️ Context7 Lokasyon Verileri oluşturuluyor...');

        // 1. Ülkeler oluştur
        $this->createCountries();

        // 2. İller oluştur
        $this->createProvinces();

        // 3. İlçeler oluştur
        $this->createDistricts();

        // 4. Mahalleler oluştur
        $this->createNeighborhoods();

        $this->command->info('✅ Context7 lokasyon verileri başarıyla oluşturuldu!');
    }

    /**
     * Ülkeler oluştur
     */
    private function createCountries(): void
    {
        $this->command->info('🌍 Ülkeler oluşturuluyor...');

        $countries = [
            [
                'id' => 1,
                'ulke_adi' => 'Türkiye',
                'ulke_kodu' => 'TR',
                'telefon_kodu' => '+90',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($countries as $country) {
            DB::table('ulkeler')->updateOrInsert(
                ['ulke_kodu' => $country['ulke_kodu']],
                $country
            );
        }

        $this->command->info('✅ ' . count($countries) . ' ülke oluşturuldu');
    }

    /**
     * İller oluştur
     */
    private function createProvinces(): void
    {
        $this->command->info('🏛️ İller oluşturuluyor...');

        $provinces = [
            // Ana iller (emlak odaklı)
            ['id' => 6, 'il_adi' => 'Ankara', 'plaka_kodu' => '06', 'ulke_id' => 1],
            ['id' => 7, 'il_adi' => 'Antalya', 'plaka_kodu' => '07', 'ulke_id' => 1],
            ['id' => 9, 'il_adi' => 'Aydın', 'plaka_kodu' => '09', 'ulke_id' => 1],
            ['id' => 34, 'il_adi' => 'İstanbul', 'plaka_kodu' => '34', 'ulke_id' => 1],
            ['id' => 35, 'il_adi' => 'İzmir', 'plaka_kodu' => '35', 'ulke_id' => 1],
            ['id' => 48, 'il_adi' => 'Muğla', 'plaka_kodu' => '48', 'ulke_id' => 1],

            // Diğer önemli iller
            ['id' => 1, 'il_adi' => 'Adana', 'plaka_kodu' => '01', 'ulke_id' => 1],
            ['id' => 16, 'il_adi' => 'Bursa', 'plaka_kodu' => '16', 'ulke_id' => 1],
            ['id' => 33, 'il_adi' => 'Mersin', 'plaka_kodu' => '33', 'ulke_id' => 1],
            ['id' => 41, 'il_adi' => 'Kocaeli', 'plaka_kodu' => '41', 'ulke_id' => 1],
        ];

        foreach ($provinces as $province) {
            $province['created_at'] = now();
            $province['updated_at'] = now();
            DB::table('iller')->updateOrInsert(
                ['plaka_kodu' => $province['plaka_kodu']],
                $province
            );
        }

        $this->command->info('✅ ' . count($provinces) . ' il oluşturuldu');
    }

    /**
     * İlçeler oluştur
     */
    private function createDistricts(): void
    {
        $this->command->info('🏘️ İlçeler oluşturuluyor...');

        $districts = [
            // Muğla İlçeleri (Ana odak)
            ['il_id' => 48, 'ilce_kodu' => '48-01', 'ilce_adi' => 'Bodrum'],
            ['il_id' => 48, 'ilce_kodu' => '48-02', 'ilce_adi' => 'Marmaris'],
            ['il_id' => 48, 'ilce_kodu' => '48-03', 'ilce_adi' => 'Fethiye'],
            ['il_id' => 48, 'ilce_kodu' => '48-04', 'ilce_adi' => 'Datça'],
            ['il_id' => 48, 'ilce_kodu' => '48-05', 'ilce_adi' => 'Ortaca'],
            ['il_id' => 48, 'ilce_kodu' => '48-06', 'ilce_adi' => 'Milas'],
            ['il_id' => 48, 'ilce_kodu' => '48-07', 'ilce_adi' => 'Köyceğiz'],
            ['il_id' => 48, 'ilce_kodu' => '48-08', 'ilce_adi' => 'Dalaman'],
            ['il_id' => 48, 'ilce_kodu' => '48-09', 'ilce_adi' => 'Menteşe'],
            ['il_id' => 48, 'ilce_kodu' => '48-10', 'ilce_adi' => 'Ula'],

            // İzmir İlçeleri
            ['il_id' => 35, 'ilce_kodu' => '35-01', 'ilce_adi' => 'Çeşme'],
            ['il_id' => 35, 'ilce_kodu' => '35-02', 'ilce_adi' => 'Karşıyaka'],
            ['il_id' => 35, 'ilce_kodu' => '35-03', 'ilce_adi' => 'Konak'],
            ['il_id' => 35, 'ilce_kodu' => '35-04', 'ilce_adi' => 'Bornova'],
            ['il_id' => 35, 'ilce_kodu' => '35-05', 'ilce_adi' => 'Urla'],

            // Aydın İlçeleri
            ['il_id' => 9, 'ilce_kodu' => '09-01', 'ilce_adi' => 'Didim'],
            ['il_id' => 9, 'ilce_kodu' => '09-02', 'ilce_adi' => 'Kuşadası'],
            ['il_id' => 9, 'ilce_kodu' => '09-03', 'ilce_adi' => 'Söke'],
            ['il_id' => 9, 'ilce_kodu' => '09-04', 'ilce_adi' => 'Efeler'],

            // İstanbul İlçeleri (Önemli olanlar)
            ['il_id' => 34, 'ilce_kodu' => '34-01', 'ilce_adi' => 'Kadıköy'],
            ['il_id' => 34, 'ilce_kodu' => '34-02', 'ilce_adi' => 'Beşiktaş'],
            ['il_id' => 34, 'ilce_kodu' => '34-03', 'ilce_adi' => 'Şişli'],
            ['il_id' => 34, 'ilce_kodu' => '34-04', 'ilce_adi' => 'Beyoğlu'],
            ['il_id' => 34, 'ilce_kodu' => '34-05', 'ilce_adi' => 'Sarıyer'],

            // Ankara İlçeleri
            ['il_id' => 6, 'ilce_kodu' => '06-01', 'ilce_adi' => 'Çankaya'],
            ['il_id' => 6, 'ilce_kodu' => '06-02', 'ilce_adi' => 'Keçiören'],
            ['il_id' => 6, 'ilce_kodu' => '06-03', 'ilce_adi' => 'Yenimahalle'],

            // Antalya İlçeleri
            ['il_id' => 7, 'ilce_kodu' => '07-01', 'ilce_adi' => 'Muratpaşa'],
            ['il_id' => 7, 'ilce_kodu' => '07-02', 'ilce_adi' => 'Konyaaltı'],
            ['il_id' => 7, 'ilce_kodu' => '07-03', 'ilce_adi' => 'Kepez'],
        ];

        foreach ($districts as $district) {
            $district['created_at'] = now();
            $district['updated_at'] = now();
            DB::table('ilceler')->updateOrInsert(
                ['ilce_kodu' => $district['ilce_kodu']],
                $district
            );
        }

        $this->command->info('✅ ' . count($districts) . ' ilçe oluşturuldu');
    }

    /**
     * Mahalleler oluştur
     */
    private function createNeighborhoods(): void
    {
        $this->command->info('🏠 Mahalleler oluşturuluyor...');

        $neighborhoods = [
            // Bodrum Mahalleleri (Ana odak)
            ['ilce_id' => 1, 'mahalle_kodu' => '48-01-01', 'mahalle_adi' => 'Gümbet', 'enlem' => 37.0317, 'boylam' => 27.4026],
            ['ilce_id' => 1, 'mahalle_kodu' => '48-01-02', 'mahalle_adi' => 'Bitez', 'enlem' => 37.0241, 'boylam' => 27.3814],
            ['ilce_id' => 1, 'mahalle_kodu' => '48-01-03', 'mahalle_adi' => 'Ortakent', 'enlem' => 37.0403, 'boylam' => 27.3303],
            ['ilce_id' => 1, 'mahalle_kodu' => '48-01-04', 'mahalle_adi' => 'Turgutreis', 'enlem' => 37.0162, 'boylam' => 27.2551],
            ['ilce_id' => 1, 'mahalle_kodu' => '48-01-05', 'mahalle_adi' => 'Yalıkavak', 'enlem' => 37.1062, 'boylam' => 27.2917],
            ['ilce_id' => 1, 'mahalle_kodu' => '48-01-06', 'mahalle_adi' => 'Gündoğan', 'enlem' => 37.0769, 'boylam' => 27.4533],
            ['ilce_id' => 1, 'mahalle_kodu' => '48-01-07', 'mahalle_adi' => 'Göltürkbükü', 'enlem' => 37.1261, 'boylam' => 27.3611],
            ['ilce_id' => 1, 'mahalle_kodu' => '48-01-08', 'mahalle_adi' => 'Gümüşlük', 'enlem' => 37.0625, 'boylam' => 27.2303],
            ['ilce_id' => 1, 'mahalle_kodu' => '48-01-09', 'mahalle_adi' => 'Konacık', 'enlem' => 37.0440, 'boylam' => 27.4054],
            ['ilce_id' => 1, 'mahalle_kodu' => '48-01-10', 'mahalle_adi' => 'Bodrum Merkez', 'enlem' => 37.0344, 'boylam' => 27.4305],

            // Marmaris Mahalleleri
            ['ilce_id' => 2, 'mahalle_kodu' => '48-02-01', 'mahalle_adi' => 'Armutalan', 'enlem' => 36.8567, 'boylam' => 28.2527],
            ['ilce_id' => 2, 'mahalle_kodu' => '48-02-02', 'mahalle_adi' => 'İçmeler', 'enlem' => 36.8001, 'boylam' => 28.2346],
            ['ilce_id' => 2, 'mahalle_kodu' => '48-02-03', 'mahalle_adi' => 'Beldibi', 'enlem' => 36.8788, 'boylam' => 28.2488],
            ['ilce_id' => 2, 'mahalle_kodu' => '48-02-04', 'mahalle_adi' => 'Turunç', 'enlem' => 36.7764, 'boylam' => 28.2492],
            ['ilce_id' => 2, 'mahalle_kodu' => '48-02-05', 'mahalle_adi' => 'Marmaris Merkez', 'enlem' => 36.8552, 'boylam' => 28.2705],

            // Fethiye Mahalleleri
            ['ilce_id' => 3, 'mahalle_kodu' => '48-03-01', 'mahalle_adi' => 'Çalış', 'enlem' => 36.6500, 'boylam' => 29.1000],
            ['ilce_id' => 3, 'mahalle_kodu' => '48-03-02', 'mahalle_adi' => 'Ölüdeniz', 'enlem' => 36.5500, 'boylam' => 29.1500],
            ['ilce_id' => 3, 'mahalle_kodu' => '48-03-03', 'mahalle_adi' => 'Göcek', 'enlem' => 36.7000, 'boylam' => 29.0500],
            ['ilce_id' => 3, 'mahalle_kodu' => '48-03-04', 'mahalle_adi' => 'Hisarönü', 'enlem' => 36.6000, 'boylam' => 29.2000],
            ['ilce_id' => 3, 'mahalle_kodu' => '48-03-05', 'mahalle_adi' => 'Kayaköy', 'enlem' => 36.5800, 'boylam' => 29.1800],

            // Çeşme Mahalleleri
            ['ilce_id' => 11, 'mahalle_kodu' => '35-01-01', 'mahalle_adi' => 'Alaçatı', 'enlem' => 38.2806, 'boylam' => 26.3785],
            ['ilce_id' => 11, 'mahalle_kodu' => '35-01-02', 'mahalle_adi' => 'Ilıca', 'enlem' => 38.2833, 'boylam' => 26.3638],
            ['ilce_id' => 11, 'mahalle_kodu' => '35-01-03', 'mahalle_adi' => 'Dalyan', 'enlem' => 38.3153, 'boylam' => 26.3172],
            ['ilce_id' => 11, 'mahalle_kodu' => '35-01-04', 'mahalle_adi' => 'Çeşme Merkez', 'enlem' => 38.3235, 'boylam' => 26.3044],

            // Didim Mahalleleri
            ['ilce_id' => 16, 'mahalle_kodu' => '09-01-01', 'mahalle_adi' => 'Altınkum', 'enlem' => 37.4000, 'boylam' => 27.2000],
            ['ilce_id' => 16, 'mahalle_kodu' => '09-01-02', 'mahalle_adi' => 'Akbük', 'enlem' => 37.3500, 'boylam' => 27.1500],
            ['ilce_id' => 16, 'mahalle_kodu' => '09-01-03', 'mahalle_adi' => 'Hisar', 'enlem' => 37.3800, 'boylam' => 27.2500],

            // Kuşadası Mahalleleri
            ['ilce_id' => 17, 'mahalle_kodu' => '09-02-01', 'mahalle_adi' => 'Kadınlar Denizi', 'enlem' => 37.8500, 'boylam' => 27.2500],
            ['ilce_id' => 17, 'mahalle_kodu' => '09-02-02', 'mahalle_adi' => 'Güzelçamlı', 'enlem' => 37.9000, 'boylam' => 27.2000],
            ['ilce_id' => 17, 'mahalle_kodu' => '09-02-03', 'mahalle_adi' => 'Davutlar', 'enlem' => 37.8000, 'boylam' => 27.3000],

            // İstanbul Mahalleleri
            ['ilce_id' => 20, 'mahalle_kodu' => '34-01-01', 'mahalle_adi' => 'Fenerbahçe', 'enlem' => 40.9000, 'boylam' => 29.0000],
            ['ilce_id' => 20, 'mahalle_kodu' => '34-01-02', 'mahalle_adi' => 'Moda', 'enlem' => 40.9500, 'boylam' => 29.0500],
            ['ilce_id' => 21, 'mahalle_kodu' => '34-02-01', 'mahalle_adi' => 'Etiler', 'enlem' => 41.0000, 'boylam' => 29.0000],
            ['ilce_id' => 21, 'mahalle_kodu' => '34-02-02', 'mahalle_adi' => 'Levent', 'enlem' => 41.0500, 'boylam' => 29.0500],
        ];

        foreach ($neighborhoods as $neighborhood) {
            $neighborhood['created_at'] = now();
            $neighborhood['updated_at'] = now();
            DB::table('mahalleler')->updateOrInsert(
                ['mahalle_kodu' => $neighborhood['mahalle_kodu']],
                $neighborhood
            );
        }

        $this->command->info('✅ ' . count($neighborhoods) . ' mahalle oluşturuldu');
    }
}
