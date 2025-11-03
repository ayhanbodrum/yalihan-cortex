<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeatureCategory;
use App\Models\Feature;
use Illuminate\Support\Str;

class FeatureCategorySeeder extends Seeder
{
    /**
     * CONTEXT7 - Özellik Kategorileri ve Özellikleri Seeder
     *
     * Mantık:
     * 1. GENEL - Tüm kategoriler için ortak
     * 2. ARSA - Sadece arsa ilanları için
     * 3. KONUT - Konut ilanları için
     * 4. TİCARİ - İşyeri ilanları için
     * 5. YAZLIK - Yazlık kiralama için
     */
    public function run(): void
    {
        $this->command->info('🎯 Özellik Kategorileri ve Özellikleri oluşturuluyor...');
        $this->command->info('');

        // ============================================
        // 1️⃣ GENEL ÖZELLİKLER (Tüm kategoriler için)
        // ============================================
        $genel = FeatureCategory::updateOrCreate(
            ['slug' => 'genel-ozellikler'],
            [
                'name' => 'Genel Özellikler',
                'description' => 'Tüm ilan tipleri için ortak özellikler',
                'icon' => 'fas fa-list',
                'order' => 1,
                'status' => true,
                'applies_to' => json_encode(['all']), // Hepsine uygulanır
            ]
        );
        $this->command->info('✅ Genel Özellikler kategorisi oluşturuldu');

        // Genel özellikler
        $this->createFeatures($genel, [
            ['name' => 'Tapu Durumu', 'slug' => 'tapu-durumu', 'input_type' => 'select', 'options' => ['Kat Mülkiyetli', 'Kat İrtifaklı', 'Arsa Tapulu', 'Hisseli']],
            ['name' => 'Kullanım Durumu', 'slug' => 'kullanim-durumu', 'input_type' => 'select', 'options' => ['Boş', 'Kiracılı', 'Mülk Sahibi']],
            ['name' => 'Krediye Uygun', 'slug' => 'krediye-uygun', 'input_type' => 'checkbox'],
            ['name' => 'Takasa Uygun', 'slug' => 'takasa-uygun', 'input_type' => 'checkbox'],
            ['name' => 'Yapı Yaşı', 'slug' => 'yapi-yasi', 'input_type' => 'number', 'unit' => 'yıl'],
        ]);

        // ============================================
        // 2️⃣ ARSA ÖZELLİKLERİ
        // ============================================
        $arsa = FeatureCategory::updateOrCreate(
            ['slug' => 'arsa-ozellikleri'],
            [
                'name' => 'Arsa Özellikleri',
                'description' => 'Arsa ilanları için özel özellikler',
                'icon' => 'fas fa-map',
                'order' => 2,
                'status' => true,
                'applies_to' => json_encode(['arsa']), // Sadece arsa
            ]
        );
        $this->command->info('✅ Arsa Özellikleri kategorisi oluşturuldu');

        $this->createFeatures($arsa, [
            ['name' => 'İmar Durumu', 'slug' => 'imar-durumu', 'input_type' => 'select', 'options' => ['İmarlı', 'İmarsız', 'Tarla', 'Bahçe', 'Müstakil Parsel']],
            ['name' => 'Ada No', 'slug' => 'ada-no', 'input_type' => 'text'],
            ['name' => 'Parsel No', 'slug' => 'parsel-no', 'input_type' => 'text'],
            ['name' => 'KAKS', 'slug' => 'kaks', 'input_type' => 'number', 'unit' => '%'],
            ['name' => 'TAKS', 'slug' => 'taks', 'input_type' => 'number', 'unit' => '%'],
            ['name' => 'Gabari', 'slug' => 'gabari', 'input_type' => 'number', 'unit' => 'm'],
            ['name' => 'Kat İzni', 'slug' => 'kat-izni', 'input_type' => 'number', 'unit' => 'kat'],
            ['name' => 'Parsel Alanı', 'slug' => 'parsel-alani', 'input_type' => 'number', 'unit' => 'm²'],
            ['name' => 'Cephe', 'slug' => 'cephe', 'input_type' => 'select', 'options' => ['Yola Cepheli', 'Denize Cepheli', 'Göle Cepheli', 'Cadde Cepheli']],
            ['name' => 'Elektrik', 'slug' => 'elektrik', 'input_type' => 'checkbox'],
            ['name' => 'Su', 'slug' => 'su', 'input_type' => 'checkbox'],
            ['name' => 'Doğalgaz', 'slug' => 'dogalgaz', 'input_type' => 'checkbox'],
        ]);

        // ============================================
        // 3️⃣ KONUT ÖZELLİKLERİ
        // ============================================
        $konut = FeatureCategory::updateOrCreate(
            ['slug' => 'konut-ozellikleri'],
            [
                'name' => 'Konut Özellikleri',
                'description' => 'Konut ilanları için özel özellikler',
                'icon' => 'fas fa-home',
                'order' => 3,
                'status' => true,
                'applies_to' => json_encode(['konut']), // Sadece konut
            ]
        );
        $this->command->info('✅ Konut Özellikleri kategorisi oluşturuldu');

        $this->createFeatures($konut, [
            ['name' => 'Oda Sayısı', 'slug' => 'oda-sayisi', 'input_type' => 'select', 'options' => ['1+0', '1+1', '2+1', '3+1', '4+1', '5+1', '6+1']],
            ['name' => 'Salon Sayısı', 'slug' => 'salon-sayisi', 'input_type' => 'number'],
            ['name' => 'Banyo Sayısı', 'slug' => 'banyo-sayisi', 'input_type' => 'number'],
            ['name' => 'Balkon Sayısı', 'slug' => 'balkon-sayisi', 'input_type' => 'number'],
            ['name' => 'Kat Numarası', 'slug' => 'kat-numarasi', 'input_type' => 'number'],
            ['name' => 'Bina Kat Sayısı', 'slug' => 'bina-kat-sayisi', 'input_type' => 'number'],
            ['name' => 'Isıtma', 'slug' => 'isitma', 'input_type' => 'select', 'options' => ['Doğalgaz', 'Kombi', 'Merkezi', 'Klima', 'Soba', 'Yerden Isıtma']],
            ['name' => 'Eşyalı', 'slug' => 'esyali', 'input_type' => 'checkbox'],
            ['name' => 'Asansör', 'slug' => 'asansor', 'input_type' => 'checkbox'],
            ['name' => 'Otopark', 'slug' => 'otopark', 'input_type' => 'checkbox'],
            ['name' => 'Güvenlik', 'slug' => 'guvenlik', 'input_type' => 'checkbox'],
            ['name' => 'Site İçi', 'slug' => 'site-ici', 'input_type' => 'checkbox'],
        ]);

        // ============================================
        // 4️⃣ TİCARİ ÖZELLİKLER
        // ============================================
        $ticari = FeatureCategory::updateOrCreate(
            ['slug' => 'ticari-ozellikler'],
            [
                'name' => 'Ticari Özellikler',
                'description' => 'İşyeri ilanları için özel özellikler',
                'icon' => 'fas fa-briefcase',
                'order' => 4,
                'status' => true,
                'applies_to' => json_encode(['isyeri']), // Sadece ticari/işyeri
            ]
        );
        $this->command->info('✅ Ticari Özellikler kategorisi oluşturuldu');

        $this->createFeatures($ticari, [
            ['name' => 'İşyeri Tipi', 'slug' => 'isyeri-tipi', 'input_type' => 'select', 'options' => ['Dükkan', 'Ofis', 'Mağaza', 'Depo', 'Fabrika', 'Atölye', 'Showroom']],
            ['name' => 'Personel Kapasitesi', 'slug' => 'personel-kapasitesi', 'input_type' => 'number', 'unit' => 'kişi'],
            ['name' => 'Ciro Bilgisi', 'slug' => 'ciro-bilgisi', 'input_type' => 'number', 'unit' => 'TL'],
            ['name' => 'Ruhsat Durumu', 'slug' => 'ruhsat-durumu', 'input_type' => 'select', 'options' => ['Var', 'Yok', 'Başvuruda']],
            ['name' => 'Yükleme Rampası', 'slug' => 'yukleme-rampasi', 'input_type' => 'checkbox'],
            ['name' => 'Cadde Cepheli', 'slug' => 'cadde-cepheli', 'input_type' => 'checkbox'],
            ['name' => 'Devren', 'slug' => 'devren', 'input_type' => 'checkbox'],
        ]);

        // ============================================
        // 5️⃣ YAZLIK ÖZELLİKLERİ
        // ============================================
        $yazlik = FeatureCategory::updateOrCreate(
            ['slug' => 'yazlik-ozellikleri'],
            [
                'name' => 'Yazlık Özellikleri',
                'description' => 'Yazlık kiralama için özel özellikler',
                'icon' => 'fas fa-umbrella-beach',
                'order' => 5,
                'status' => true,
                'applies_to' => json_encode(['yazlik-kiralama']), // Sadece yazlık
            ]
        );
        $this->command->info('✅ Yazlık Özellikleri kategorisi oluşturuldu');

        $this->createFeatures($yazlik, [
            ['name' => 'Havuz', 'slug' => 'havuz', 'input_type' => 'select', 'options' => ['Özel Havuz', 'Ortak Havuz', 'Havuz Yok']],
            ['name' => 'Deniz Mesafesi', 'slug' => 'deniz-mesafesi', 'input_type' => 'number', 'unit' => 'm'],
            ['name' => 'Deniz Manzarası', 'slug' => 'deniz-manzarasi', 'input_type' => 'checkbox'],
            ['name' => 'Yatak Sayısı', 'slug' => 'yatak-sayisi', 'input_type' => 'number'],
            ['name' => 'Maksimum Misafir', 'slug' => 'maksimum-misafir', 'input_type' => 'number', 'unit' => 'kişi'],
            ['name' => 'Minimum Konaklama', 'slug' => 'minimum-konaklama', 'input_type' => 'number', 'unit' => 'gün'],
            ['name' => 'Klima', 'slug' => 'klima', 'input_type' => 'checkbox'],
            ['name' => 'WiFi', 'slug' => 'wifi', 'input_type' => 'checkbox'],
            ['name' => 'Barbekü', 'slug' => 'barbeku', 'input_type' => 'checkbox'],
            ['name' => 'Bahçe', 'slug' => 'bahce', 'input_type' => 'checkbox'],
        ]);

        $this->command->info('');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✅ Tüm özellik kategorileri ve özellikleri oluşturuldu!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');

        // Özet
        $totalCategories = FeatureCategory::count();
        $totalFeatures = Feature::count();
        $this->command->info("📊 Özet:");
        $this->command->info("   - Kategori Sayısı: {$totalCategories}");
        $this->command->info("   - Özellik Sayısı: {$totalFeatures}");
    }

    /**
     * Helper: Özellikleri toplu oluştur
     */
    private function createFeatures(FeatureCategory $category, array $features): void
    {
        foreach ($features as $index => $feature) {
            Feature::updateOrCreate(
                ['slug' => $feature['slug']],
                [
                    'name' => $feature['name'],
                    'feature_category_id' => $category->id,
                    'type' => $feature['input_type'] ?? 'text',
                    'options' => isset($feature['options']) ? json_encode($feature['options']) : null,
                    'unit' => $feature['unit'] ?? null,
                'is_required' => false,
                'is_filterable' => true,
                    'is_searchable' => false,
                    'order' => $index + 1,
                'status' => true,
                ]
            );
        }

        $count = count($features);
        $this->command->info("   └─ {$count} özellik eklendi");
    }
}
