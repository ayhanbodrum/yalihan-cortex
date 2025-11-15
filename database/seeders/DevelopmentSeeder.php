<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeatureCategory;
use App\Models\OzellikKategori;
use App\Models\Feature;
use App\Models\FeatureTranslation;
use Illuminate\Support\Str;

/**
 * Development Environment Seeder
 *
 * Creates consistent test data for development and testing
 * Run with: php artisan db:seed --class=DevelopmentSeeder
 */
class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Development Seeder başlatılıyor...');

        // Seed in specific order due to dependencies
        $this->seedFeatureCategories();
        $this->seedOzellikKategoriler();
        $this->seedFeatures();

        $this->command->info('✅ Development Seeder tamamlandı!');
        $this->showSummary();
    }

    /**
     * Seed FeatureCategory with translations
     */
    protected function seedFeatureCategories(): void
    {
        $this->command->info('📂 FeatureCategory verisi oluşturuluyor...');

        $categories = [
            ['name' => 'Genel Özellikler', 'slug' => 'genel-ozellikler', 'description' => 'Tüm emlak türleri için genel özellikler'],
            ['name' => 'Konut Özellikleri', 'slug' => 'konut-ozellikleri', 'description' => 'Konut türü emlaklar için özel özellikler'],
            ['name' => 'Arsa Özellikleri', 'slug' => 'arsa-ozellikleri', 'description' => 'Arsa türü emlaklar için özel özellikler'],
            ['name' => 'İş Yeri Özellikleri', 'slug' => 'isyeri-ozellikleri', 'description' => 'İş yeri türü emlaklar için özel özellikler'],
            ['name' => 'Yazlık Özellikleri', 'slug' => 'yazlik-ozellikleri', 'description' => 'Yazlık türü emlaklar için özel özellikler'],
            ['name' => 'Lokasyon Özellikleri', 'slug' => 'lokasyon-ozellikleri', 'description' => 'Konum ve çevre özellikleri'],
            ['name' => 'Teknik Özellikler', 'slug' => 'teknik-ozellikler', 'description' => 'Teknik altyapı özellikleri'],
        ];

        foreach ($categories as $index => $categoryData) {
            $category = FeatureCategory::updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'display_order' => $index + 1,
                    'status' => true,
                ]
            );

            $this->command->line("  ✓ {$categoryData['name']}");
        }

        $this->command->info('📂 '.count($categories).' FeatureCategory oluşturuldu.');
    }

    /**
     * Seed OzellikKategori
     */
    protected function seedOzellikKategoriler(): void
    {
        $this->command->info('🏷️ OzellikKategori verisi oluşturuluyor...');

        $ozellikKategoriler = [
            ['ad' => 'Temel Bilgiler', 'sira' => 1, 'aciklama' => 'Temel özellik bilgileri', 'icon' => '📋', 'veri_tipi' => 'text'],
            ['ad' => 'Oda ve Alan', 'sira' => 2, 'aciklama' => 'Oda sayısı ve alan bilgileri', 'icon' => '🏠', 'veri_tipi' => 'number'],
            ['ad' => 'Konum ve Çevre', 'sira' => 3, 'aciklama' => 'Konum ve çevre özellikleri', 'icon' => '📍', 'veri_tipi' => 'text'],
            ['ad' => 'Ek Özellikler', 'sira' => 4, 'aciklama' => 'Diğer özellikler', 'icon' => '⭐', 'veri_tipi' => 'boolean'],
            ['ad' => 'Fiyat ve Ödeme', 'sira' => 5, 'aciklama' => 'Fiyat ve ödeme detayları', 'icon' => '💰', 'veri_tipi' => 'number'],
            ['ad' => 'Güvenlik', 'sira' => 6, 'aciklama' => 'Güvenlik özellikleri', 'icon' => '🔒', 'veri_tipi' => 'boolean'],
            ['ad' => 'Sosyal Alanlar', 'sira' => 7, 'aciklama' => 'Sosyal alan özellikleri', 'icon' => '🎾', 'veri_tipi' => 'select'],
            ['ad' => 'Ulaşım', 'sira' => 8, 'aciklama' => 'Ulaşım ve park özellikleri', 'icon' => '🚗', 'veri_tipi' => 'text'],
        ];

        foreach ($ozellikKategoriler as $oz) {
            OzellikKategori::updateOrCreate(
                ['slug' => Str::slug($oz['ad'])],
                [
                    'ad' => $oz['ad'],
                    'sira' => $oz['sira'],
                    'status' => true,
                    'aciklama' => $oz['aciklama'],
                    'icon' => $oz['icon'],
                    'veri_tipi' => $oz['veri_tipi'],
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => rand(0, 1) == 1,
                    'detay_sayfasinda_goster' => true,
                ]
            );

            $this->command->line("  ✓ {$oz['ad']}");
        }

        $this->command->info("🏷️ " . count($ozellikKategoriler) . " OzellikKategori oluşturuldu.");
    }

    /**
     * Seed Features with various types
     */
    protected function seedFeatures(): void
    {
        $this->command->info('⚡ Feature verisi oluşturuluyor...');

        $categories = FeatureCategory::all();
        $ozellikKategoriler = OzellikKategori::all();

        if ($categories->isEmpty() || $ozellikKategoriler->isEmpty()) {
            $this->command->error('❌ Kategoriler bulunamadı! Önce kategorileri oluşturun.');
            return;
        }

        $features = [
            // Text features
            ['name' => 'Yapı Yaşı', 'type' => 'number', 'description' => 'Binanın yaşı'],
            ['name' => 'Kat Sayısı', 'type' => 'number', 'description' => 'Toplam kat sayısı'],
            ['name' => 'Bulunduğu Kat', 'type' => 'number', 'description' => 'Dairenin bulunduğu kat'],

            // Select features
            ['name' => 'Oda Sayısı', 'type' => 'select', 'options' => '1+0,1+1,2+1,3+1,4+1,5+1,6+1', 'description' => 'Oda ve salon sayısı'],
            ['name' => 'Banyo Sayısı', 'type' => 'select', 'options' => '1,2,3,4,5+', 'description' => 'Banyo sayısı'],
            ['name' => 'Isıtma Tipi', 'type' => 'select', 'options' => 'Merkezi,Kombi,Soba,Klima,Yerden Isıtma', 'description' => 'Isıtma sistemi türü'],
            ['name' => 'Yakıt Tipi', 'type' => 'select', 'options' => 'Doğalgaz,Elektrik,Kömür,Fuel-oil,LPG', 'description' => 'Kullanılan yakıt türü'],

            // Boolean features
            ['name' => 'Eşyalı', 'type' => 'boolean', 'description' => 'Eşyalı olarak kiraya verilip verilmeyeceği'],
            ['name' => 'Balkon', 'type' => 'boolean', 'description' => 'Balkon mevcut mu'],
            ['name' => 'Asansör', 'type' => 'boolean', 'description' => 'Asansör mevcut mu'],
            ['name' => 'Otopark', 'type' => 'boolean', 'description' => 'Otopark mevcut mu'],
            ['name' => 'Güvenlik', 'type' => 'boolean', 'description' => '24 saat güvenlik'],
            ['name' => 'Yüzme Havuzu', 'type' => 'boolean', 'description' => 'Yüzme havuzu mevcut mu'],

            // Number features
            ['name' => 'Net Metrekare', 'type' => 'number', 'description' => 'Net kullanım alanı (m²)'],
            ['name' => 'Brüt Metrekare', 'type' => 'number', 'description' => 'Brüt alan (m²)'],

            // Text features
            ['name' => 'Cephe Yönü', 'type' => 'select', 'options' => 'Kuzey,Güney,Doğu,Batı,Güneydoğu,Güneybatı,Kuzeydoğu,Kuzeybatı', 'description' => 'Binanın cephe yönü'],
            ['name' => 'Manzara', 'type' => 'select', 'options' => 'Deniz,Doğa,Şehir,Sokak,Avlu', 'description' => 'Manzara türü'],

            // Arsa özellikleri
            ['name' => 'Arsa Alanı', 'type' => 'number', 'description' => 'Arsanın toplam alanı (m²)'],
            ['name' => 'İmar Durumu', 'type' => 'select', 'options' => 'Konut İmarlı,Ticari İmarlı,Sanayi İmarlı,İmarsız', 'description' => 'Arsanın imar statusu'],
            ['name' => 'Tapu Durumu', 'type' => 'select', 'options' => 'Kat Mülkiyetli,Kat İrtifaklı,Arsa Tapulu,Hisseli Tapu', 'description' => 'Tapu türü'],
        ];

        foreach ($features as $index => $featureData) {
            $category = $categories->random();
            $ozellikKategori = $ozellikKategoriler->random();

            $feature = Feature::updateOrCreate(
                ['slug' => Str::slug($featureData['name'])],
                [
                    'category_id' => $category->id,
                    'kategori_id' => $ozellikKategori->id,
                    'type' => $featureData['type'],
                    'options' => $featureData['options'] ?? null,
                    'status' => true,
                    'is_required' => rand(0, 1) == 1,
                    'is_filterable' => true,
                    'show_on_card' => rand(0, 1) == 1,
                    'display_order' => $index + 1,
                ]
            );

            // Create translation
            FeatureTranslation::updateOrCreate(
                [
                    'feature_id' => $feature->id,
                    'locale' => 'tr'
                ],
                [
                    'name' => $featureData['name'],
                    'description' => $featureData['description'] ?? null,
                ]
            );

            $this->command->line("  ✓ {$featureData['name']} ({$featureData['type']})");
        }

        $this->command->info("⚡ " . count($features) . " Feature oluşturuldu.");
    }

    /**
     * Show summary of created records
     */
    protected function showSummary(): void
    {
        $this->command->info('');
        $this->command->info('📊 ÖZET RAPOR:');
        $this->command->line('  FeatureCategory: ' . FeatureCategory::count());
        $this->command->line('  OzellikKategori: ' . OzellikKategori::count());
        $this->command->line('  Feature: ' . Feature::count());
        $this->command->line('  FeatureTranslation: ' . FeatureTranslation::count());
        $this->command->info('');
        $this->command->info('🎉 Artık /admin/ozellikler/create sayfası tam çalışır statusda!');
        $this->command->info('💡 Test etmek için: http://127.0.0.1:8000/admin/ozellikler/create');
    }

    /**
     * Clear existing data if needed
     */
    public function clearData(): void
    {
        $this->command->warn('🗑️ Mevcut test verisi temizleniyor...');

        FeatureTranslation::truncate();
        Feature::truncate();
        OzellikKategori::truncate();
        FeatureCategory::truncate();

        $this->command->info('✅ Veri temizlendi.');
    }
}
