<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;
use App\Models\FeatureCategory;
use Illuminate\Support\Str;

class CategorySpecificFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Bu seeder, kategori-özel özellikleri ekler:
     * - Arsa özellikleri (imar, ada/parsel, KAKS, vb.)
     * - Konut özellikleri (oda sayısı, kat, ısınma, vb.)
     * - Kiralık özellikleri (depozito, aidat, eşyalı/eşyasız, vb.)
     */
    public function run(): void
    {
        // ==========================================
        // 1. ARSA ÖZELLİKLERİ
        // ==========================================
        $this->seedArsaFeatures();

        // ==========================================
        // 2. KONUT ÖZELLİKLERİ
        // ==========================================
        $this->seedKonutFeatures();

        // ==========================================
        // 3. KİRALIK ÖZELLİKLERİ
        // ==========================================
        $this->seedKiralikFeatures();

        $this->command->info('✅ Kategori-özel özellikler başarıyla eklendi!');
    }

    /**
     * ARSA ÖZELLİKLERİ
     */
    private function seedArsaFeatures()
    {
        // ✅ Context7: Arsa kategorisi oluştur (applies_to: string format)
        $arsaCategory = FeatureCategory::firstOrCreate(
            ['slug' => 'arsa-ozellikleri'],
            [
                'name' => 'Arsa Özellikleri',
                'description' => 'Arsa ilanları için özel özellikler',
                'icon' => 'fas fa-mountain',
                'display_order' => 1,
                'applies_to' => 'arsa', // ✅ Context7: string format (not JSON)
                'status' => true
            ]
        );

        $arsaFeatures = [
            // İmar Durumu
            [
                'name' => 'İmar Durumu',
                'slug' => 'imar-durumu',
                'description' => 'Arsanın imar durumu',
                'type' => 'select',
                'options' => json_encode([
                    'İmarlı',
                    'İmarsız',
                    'Villa İmarlı',
                    'Konut İmarlı',
                    'Ticari İmarlı',
                    'Sanayi İmarlı',
                    'Turizm İmarlı',
                    'Tarla',
                    'Müstakil İmarlı'
                ]),
                'is_required' => true,
                'is_filterable' => true,
                'display_order' => 1,
                'applies_to' => 'arsa'
            ],
            // Ada No
            [
                'name' => 'Ada No',
                'slug' => 'ada-no',
                'description' => 'Tapu ada numarası',
                'type' => 'text',
                'is_required' => false,
                'is_filterable' => false,
                'display_order' => 2,
                'applies_to' => 'arsa'
            ],
            // Parsel No
            [
                'name' => 'Parsel No',
                'slug' => 'parsel-no',
                'description' => 'Tapu parsel numarası',
                'type' => 'text',
                'is_required' => false,
                'is_filterable' => false,
                'display_order' => 3,
                'applies_to' => 'arsa'
            ],
            // KAKS (Kat Alanları Kat Sayısı)
            [
                'name' => 'KAKS',
                'slug' => 'kaks',
                'description' => 'Kat Alanları Kat Sayısı (Emsal)',
                'type' => 'number',
                'unit' => 'kat',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 4,
                'applies_to' => 'arsa'
            ],
            // Gabari
            [
                'name' => 'Gabari',
                'slug' => 'gabari',
                'description' => 'Maksimum bina yüksekliği',
                'type' => 'number',
                'unit' => 'metre',
                'is_required' => false,
                'is_filterable' => false,
                'display_order' => 5,
                'applies_to' => 'arsa'
            ],
            // Taban Alanı Katsayısı
            [
                'name' => 'Taban Alanı Katsayısı (TAKS)',
                'slug' => 'taks',
                'description' => 'Taban alanı katsayısı',
                'type' => 'number',
                'unit' => 'oran',
                'is_required' => false,
                'is_filterable' => false,
                'display_order' => 6,
                'applies_to' => 'arsa'
            ],
            // Tapu Durumu
            [
                'name' => 'Tapu Durumu',
                'slug' => 'tapu-durumu',
                'description' => 'Tapunun mevcut durumu',
                'type' => 'select',
                'options' => json_encode([
                    'Kat Mülkiyetli',
                    'Kat İrtifaklı',
                    'Arsa Tapulu',
                    'Hisseli Tapu',
                    'Müstakil Tapu',
                    'Tahsisli'
                ]),
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 7,
                'applies_to' => 'arsa'
            ],
            // Elektrik
            [
                'name' => 'Elektrik',
                'slug' => 'elektrik-var',
                'description' => 'Elektrik altyapısı mevcut mu?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 8,
                'applies_to' => 'arsa'
            ],
            // Su
            [
                'name' => 'Su',
                'slug' => 'su-var',
                'description' => 'Su altyapısı mevcut mu?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 9,
                'applies_to' => 'arsa'
            ],
            // Doğalgaz
            [
                'name' => 'Doğalgaz',
                'slug' => 'dogalgaz-var',
                'description' => 'Doğalgaz altyapısı mevcut mu?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 10,
                'applies_to' => 'arsa'
            ],
            // Kanalizasyon
            [
                'name' => 'Kanalizasyon',
                'slug' => 'kanalizasyon-var',
                'description' => 'Kanalizasyon altyapısı mevcut mu?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 11,
                'applies_to' => 'arsa'
            ],
            // Yol
            [
                'name' => 'Yol',
                'slug' => 'yol-var',
                'description' => 'Asfalt/beton yol mevcut mu?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 12,
                'applies_to' => 'arsa'
            ],
            // Deniz Manzarası
            [
                'name' => 'Deniz Manzarası',
                'slug' => 'deniz-manzarasi-arsa',
                'description' => 'Arsa deniz manzaralı mı?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 13,
                'applies_to' => 'arsa'
            ],
        ];

        foreach ($arsaFeatures as $feature) {
            Feature::updateOrCreate(
                ['slug' => $feature['slug']],
                array_merge($feature, ['feature_category_id' => $arsaCategory->id, 'status' => true])
            );
        }

        $this->command->info('✅ Arsa özellikleri eklendi');
    }

    /**
     * KONUT ÖZELLİKLERİ
     */
    private function seedKonutFeatures()
    {
        // Konut kategorisi oluştur
        $konutCategory = FeatureCategory::firstOrCreate(
            ['slug' => 'konut-ozellikleri'],
            [
                'name' => 'Konut Özellikleri',
                'description' => 'Konut (Daire/Villa) ilanları için özel özellikler',
                'icon' => 'fas fa-home',
                'display_order' => 2,
                'applies_to' => 'konut',
                'status' => true
            ]
        );

        $konutFeatures = [
            // Oda Sayısı
            [
                'name' => 'Oda Sayısı',
                'slug' => 'oda-sayisi',
                'description' => 'Toplam oda sayısı (salon hariç)',
                'type' => 'select',
                'options' => json_encode([
                    'Stüdyo (1+0)',
                    '1+1',
                    '2+1',
                    '3+1',
                    '4+1',
                    '5+1',
                    '6+1 ve üzeri'
                ]),
                'is_required' => true,
                'is_filterable' => true,
                'display_order' => 1,
                'applies_to' => 'konut'
            ],
            // Brüt M²
            [
                'name' => 'Brüt M²',
                'slug' => 'brut-metrekare',
                'description' => 'Brüt metrekare',
                'type' => 'number',
                'unit' => 'm²',
                'is_required' => true,
                'is_filterable' => true,
                'display_order' => 2,
                'applies_to' => 'konut'
            ],
            // Net M²
            [
                'name' => 'Net M²',
                'slug' => 'net-metrekare',
                'description' => 'Net metrekare',
                'type' => 'number',
                'unit' => 'm²',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 3,
                'applies_to' => 'konut'
            ],
            // Banyo Sayısı
            [
                'name' => 'Banyo Sayısı',
                'slug' => 'banyo-sayisi',
                'description' => 'Toplam banyo/WC sayısı',
                'type' => 'number',
                'unit' => 'adet',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 4,
                'applies_to' => 'konut'
            ],
            // Bulunduğu Kat
            [
                'name' => 'Bulunduğu Kat',
                'slug' => 'bulundugu-kat',
                'description' => 'Dairenin bulunduğu kat',
                'type' => 'select',
                'options' => json_encode([
                    'Bodrum Kat',
                    'Zemin Kat',
                    'Bahçe Katı',
                    '1. Kat',
                    '2. Kat',
                    '3. Kat',
                    '4. Kat',
                    '5. Kat',
                    '6-10 Kat arası',
                    '11-15 Kat arası',
                    '16 Kat ve üzeri',
                    'Villa/Müstakil'
                ]),
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 5,
                'applies_to' => 'konut'
            ],
            // Kat Sayısı
            [
                'name' => 'Kat Sayısı',
                'slug' => 'kat-sayisi',
                'description' => 'Binanın toplam kat sayısı',
                'type' => 'number',
                'unit' => 'kat',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 6,
                'applies_to' => 'konut'
            ],
            // Bina Yaşı
            [
                'name' => 'Bina Yaşı',
                'slug' => 'bina-yasi',
                'description' => 'Binanın yaşı',
                'type' => 'select',
                'options' => json_encode([
                    '0 (Yeni)',
                    '1-5 Yıl',
                    '6-10 Yıl',
                    '11-15 Yıl',
                    '16-20 Yıl',
                    '21-25 Yıl',
                    '26-30 Yıl',
                    '31 Yıl ve üzeri'
                ]),
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 7,
                'applies_to' => 'konut'
            ],
            // Isınma Tipi
            [
                'name' => 'Isınma Tipi',
                'slug' => 'isinma-tipi',
                'description' => 'Isınma sistemi türü',
                'type' => 'select',
                'options' => json_encode([
                    'Doğalgaz (Kombi)',
                    'Merkezi Sistem',
                    'Yerden Isıtma',
                    'Klima',
                    'Soba',
                    'Elektrikli Isıtma',
                    'Jeotermal',
                    'Güneş Enerjisi'
                ]),
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 8,
                'applies_to' => 'konut'
            ],
            // Balkon
            [
                'name' => 'Balkon',
                'slug' => 'balkon-var',
                'description' => 'Balkon mevcut mu?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 9,
                'applies_to' => 'konut'
            ],
            // Asansör
            [
                'name' => 'Asansör',
                'slug' => 'asansor-var',
                'description' => 'Binada asansör var mı?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 10,
                'applies_to' => 'konut'
            ],
            // Otopark
            [
                'name' => 'Otopark',
                'slug' => 'otopark-var',
                'description' => 'Otopark mevcut mu?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 11,
                'applies_to' => 'konut'
            ],
            // Site İçi
            [
                'name' => 'Site İçi',
                'slug' => 'site-ici',
                'description' => 'Site içerisinde mi?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 12,
                'applies_to' => 'konut'
            ],
            // Güvenlik
            [
                'name' => 'Güvenlik',
                'slug' => 'guvenlik-var',
                'description' => '7/24 güvenlik mevcut mu?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 13,
                'applies_to' => 'konut'
            ],
            // Havuz
            [
                'name' => 'Havuz',
                'slug' => 'havuz-var-konut',
                'description' => 'Havuz mevcut mu?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 14,
                'applies_to' => 'konut'
            ],
        ];

        foreach ($konutFeatures as $feature) {
            Feature::updateOrCreate(
                ['slug' => $feature['slug']],
                array_merge($feature, ['feature_category_id' => $konutCategory->id, 'status' => true])
            );
        }

        $this->command->info('✅ Konut özellikleri eklendi');
    }

    /**
     * KİRALIK ÖZELLİKLERİ
     */
    private function seedKiralikFeatures()
    {
        // Kiralık kategorisi oluştur
        $kiralikCategory = FeatureCategory::firstOrCreate(
            ['slug' => 'kiralik-ozellikleri'],
            [
                'name' => 'Kiralık Özellikleri',
                'description' => 'Kiralık ilanlar için özel özellikler',
                'icon' => 'fas fa-key',
                'display_order' => 3,
                'applies_to' => 'kiralik',
                'status' => true
            ]
        );

        $kiralikFeatures = [
            // Depozito
            [
                'name' => 'Depozito',
                'slug' => 'depozito',
                'description' => 'Depozito tutarı (ay cinsinden)',
                'type' => 'number',
                'unit' => 'ay',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 1,
                'applies_to' => 'kiralik'
            ],
            // Aidat
            [
                'name' => 'Aidat',
                'slug' => 'aidat',
                'description' => 'Aylık aidat tutarı',
                'type' => 'number',
                'unit' => 'TL',
                'is_required' => false,
                'is_filterable' => false,
                'display_order' => 2,
                'applies_to' => 'kiralik'
            ],
            // Elektrik Faturası Dahil Mi?
            [
                'name' => 'Elektrik Dahil',
                'slug' => 'elektrik-dahil',
                'description' => 'Elektrik faturası kiraya dahil mi?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 3,
                'applies_to' => 'kiralik'
            ],
            // Su Faturası Dahil Mi?
            [
                'name' => 'Su Dahil',
                'slug' => 'su-dahil',
                'description' => 'Su faturası kiraya dahil mi?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 4,
                'applies_to' => 'kiralik'
            ],
            // Doğalgaz Dahil Mi?
            [
                'name' => 'Doğalgaz Dahil',
                'slug' => 'dogalgaz-dahil',
                'description' => 'Doğalgaz faturası kiraya dahil mi?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 5,
                'applies_to' => 'kiralik'
            ],
            // İnternet Dahil Mi?
            [
                'name' => 'İnternet Dahil',
                'slug' => 'internet-dahil-kiralik',
                'description' => 'İnternet kiraya dahil mi?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 6,
                'applies_to' => 'kiralik'
            ],
            // Eşyalı/Eşyasız
            [
                'name' => 'Eşyalı Mı?',
                'slug' => 'esyali-mi',
                'description' => 'Ev eşyalı mı eşyasız mı?',
                'type' => 'select',
                'options' => json_encode([
                    'Eşyalı',
                    'Eşyasız',
                    'Yarı Eşyalı'
                ]),
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 7,
                'applies_to' => 'kiralik'
            ],
            // Kira Süresi
            [
                'name' => 'Kira Süresi',
                'slug' => 'kira-suresi',
                'description' => 'Minimum kira süresi',
                'type' => 'select',
                'options' => json_encode([
                    'Günlük',
                    'Haftalık',
                    'Aylık',
                    '6 Ay',
                    '1 Yıl',
                    '2 Yıl',
                    'Belirsiz'
                ]),
                'is_required' => false,
                'is_filterable' => true,
                'display_order' => 8,
                'applies_to' => 'kiralik'
            ],
            // Tahliye Tarihi
            [
                'name' => 'Tahliye Tarihi',
                'slug' => 'tahliye-tarihi',
                'description' => 'Kiracı tahliye tarihi',
                'type' => 'text',
                'is_required' => false,
                'is_filterable' => false,
                'display_order' => 9,
                'applies_to' => 'kiralik'
            ],
            // Ön Ödeme
            [
                'name' => 'Ön Ödeme Gerekli Mi?',
                'slug' => 'on-odeme',
                'description' => 'Ön ödeme talep ediliyor mu?',
                'type' => 'boolean',
                'is_required' => false,
                'is_filterable' => false,
                'display_order' => 10,
                'applies_to' => 'kiralik'
            ],
        ];

        foreach ($kiralikFeatures as $feature) {
            Feature::updateOrCreate(
                ['slug' => $feature['slug']],
                array_merge($feature, ['feature_category_id' => $kiralikCategory->id, 'status' => true])
            );
        }

        $this->command->info('✅ Kiralık özellikleri eklendi');
    }
}
