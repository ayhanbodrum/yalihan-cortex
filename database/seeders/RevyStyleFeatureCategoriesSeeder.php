<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\FeatureCategory;
use App\Models\Feature;
use Illuminate\Support\Str;

/**
 * Revy.com.tr Tarzı Özellik Kategorileri Seeder
 *
 * Modal tabanlı özellik seçim sistemi için kategoriler:
 * - İç Özellikleri (İç Mekan)
 * - Dış Özellikleri (Dış Mekan)
 * - Muhit (Çevre)
 * - Ulaşım (Transportation)
 * - Cephe (Facade/Frontage)
 * - Manzara (View/Scenery)
 *
 * Context7 Standardı: C7-REVY-FEATURE-CATEGORIES-2025-11-05
 * Versiyon: 1.0.0
 */
class RevyStyleFeatureCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎨 Revy.com.tr Tarzı Özellik Kategorileri oluşturuluyor...');
        $this->command->info('');

        // Context7: Schema kontrolü
        $hasStatusColumn = Schema::hasColumn('feature_categories', 'status');
        $hasEnabledColumn = Schema::hasColumn('feature_categories', 'enabled');

        // ============================================
        // 1️⃣ İÇ ÖZELLİKLERİ (Interior Features)
        // ============================================
        $icOzellikleriData = [
            'name' => 'İç Özellikleri',
            'slug' => 'ic-ozellikleri',
            'description' => 'İç mekan özellikleri (ADSL, Asansör, Balkon, vb.)',
            'icon' => 'fas fa-home',
            // ✅ Context7: 'type' field'ı migration'da yok, kaldırıldı
            'display_order' => 10,
        ];

        if ($hasStatusColumn) {
            $icOzellikleriData['status'] = true;
        } elseif ($hasEnabledColumn) {
            $icOzellikleriData['enabled'] = true;
        }

        $icOzellikleri = FeatureCategory::updateOrCreate(
            ['slug' => 'ic-ozellikleri'],
            $icOzellikleriData
        );
        $this->command->info('✅ İç Özellikleri kategorisi oluşturuldu');

        // İç Özellikleri
        $this->createFeatures($icOzellikleri, [
            ['name' => 'ADSL', 'slug' => 'adsl'],
            ['name' => 'Ahşap Doğrama', 'slug' => 'ahsap-dograma'],
            ['name' => 'Akıllı Ev', 'slug' => 'akilli-ev'],
            ['name' => 'Alarm (Hırsız)', 'slug' => 'alarm-hirsiz'],
            ['name' => 'Alarm (Yangın)', 'slug' => 'alarm-yangin'],
            ['name' => 'Alüminyum Doğrama', 'slug' => 'aluminyum-dograma'],
            ['name' => 'Amerikan Kapı', 'slug' => 'amerikan-kapi'],
            ['name' => 'Amerikan Mutfak', 'slug' => 'amerikan-mutfak'],
            ['name' => 'Ankastre Mutfak', 'slug' => 'ankastre-mutfak'],
            ['name' => 'Asansör', 'slug' => 'asansor'],
            ['name' => 'Balkon', 'slug' => 'balkon'],
            ['name' => 'Barbekü', 'slug' => 'barbeku'],
            ['name' => 'Beyaz Eşya', 'slug' => 'beyaz-esya'],
            ['name' => 'Boyalı', 'slug' => 'boyali'],
            ['name' => 'Bulaşık Makinesi', 'slug' => 'bulasik-makinesi'],
            ['name' => 'Buzdolabı', 'slug' => 'buzdolabi'],
            ['name' => 'Çamaşır Kurutma Makinesi', 'slug' => 'camasir-kurutma-makinesi'],
            ['name' => 'Çamaşır Makinesi', 'slug' => 'camasir-makinesi'],
            ['name' => 'Çamaşır Odası', 'slug' => 'camasir-odasi'],
            ['name' => 'Çelik Kapı', 'slug' => 'celik-kapi'],
            ['name' => 'Duşakabin', 'slug' => 'dusakabin'],
            ['name' => 'Duvar Kağıdı', 'slug' => 'duvar-kagidi'],
            ['name' => 'Ebeveyn Banyosu', 'slug' => 'ebeveyn-banyosu'],
            ['name' => 'Fırın', 'slug' => 'firin'],
            ['name' => 'Fiber İnternet', 'slug' => 'fiber-internet'],
            ['name' => 'Fransız Balkon', 'slug' => 'fransiz-balkon'],
            ['name' => 'Giyinme Odası', 'slug' => 'giyinme-odasi'],
            ['name' => 'Gömme Dolap', 'slug' => 'gomme-dolap'],
            ['name' => 'Görüntülü Diafon', 'slug' => 'goruntulu-diafon'],
            ['name' => 'Hilton Banyo', 'slug' => 'hilton-banyo'],
            ['name' => 'Halı Kaplama', 'slug' => 'hali-kaplama'],
            ['name' => 'Hazır Mutfak', 'slug' => 'hazir-mutfak'],
            ['name' => 'Intercom Sistemi', 'slug' => 'intercom-sistemi'],
            ['name' => 'Isıcam', 'slug' => 'isicam'],
            ['name' => 'Jakuzi', 'slug' => 'jakuzi'],
        ]);

        // ============================================
        // 2️⃣ DIŞ ÖZELLİKLERİ (Exterior Features)
        // ============================================
        $disOzellikleriData = [
            'name' => 'Dış Özellikleri',
            'slug' => 'dis-ozellikleri',
            'description' => 'Dış mekan özellikleri (Bahçe, Otopark, Güvenlik, vb.)',
            'icon' => 'fas fa-tree',
            // ✅ Context7: 'type' field'ı migration'da yok, kaldırıldı
            'display_order' => 20,
        ];

        if ($hasStatusColumn) {
            $disOzellikleriData['status'] = true;
        } elseif ($hasEnabledColumn) {
            $disOzellikleriData['enabled'] = true;
        }

        $disOzellikleri = FeatureCategory::updateOrCreate(
            ['slug' => 'dis-ozellikleri'],
            $disOzellikleriData
        );
        $this->command->info('✅ Dış Özellikleri kategorisi oluşturuldu');

        // Dış Özellikleri
        $this->createFeatures($disOzellikleri, [
            ['name' => 'Bahçe', 'slug' => 'bahce'],
            ['name' => 'Otopark', 'slug' => 'otopark'],
            ['name' => 'Kapalı Otopark', 'slug' => 'kapali-otopark'],
            ['name' => 'Açık Otopark', 'slug' => 'acik-otopark'],
            ['name' => 'Güvenlik', 'slug' => 'guvenlik'],
            ['name' => 'Güvenlik Sistemi', 'slug' => 'guvenlik-sistemi'],
            ['name' => 'Kamera Güvenliği', 'slug' => 'kamera-guvenligi'],
            ['name' => 'Bahçe Aydınlatması', 'slug' => 'bahce-aydinlatmasi'],
            ['name' => 'Site İçi', 'slug' => 'site-ici'],
            ['name' => 'Ortak Havuz', 'slug' => 'ortak-havuz'],
            ['name' => 'Ortak Bahçe', 'slug' => 'ortak-bahce'],
            ['name' => 'Teras', 'slug' => 'teras'],
            ['name' => 'Balkon', 'slug' => 'balkon-dis'],
        ]);

        // ============================================
        // 3️⃣ MUHİT (Neighborhood/Environment)
        // ============================================
        $muhitData = [
            'name' => 'Muhit',
            'slug' => 'muhit',
            'description' => 'Çevre ve sosyal alanlar',
            'icon' => 'fas fa-map-marker-alt',
            // ✅ Context7: 'type' field'ı migration'da yok, kaldırıldı
            'display_order' => 30,
        ];

        if ($hasStatusColumn) {
            $muhitData['status'] = true;
        } elseif ($hasEnabledColumn) {
            $muhitData['enabled'] = true;
        }

        $muhit = FeatureCategory::updateOrCreate(
            ['slug' => 'muhit'],
            $muhitData
        );
        $this->command->info('✅ Muhit kategorisi oluşturuldu');

        // Muhit Özellikleri
        $this->createFeatures($muhit, [
            ['name' => 'Okul', 'slug' => 'okul'],
            ['name' => 'Hastane', 'slug' => 'hastane'],
            ['name' => 'Market', 'slug' => 'market'],
            ['name' => 'AVM', 'slug' => 'avm'],
            ['name' => 'Park', 'slug' => 'park'],
            ['name' => 'Spor Salonu', 'slug' => 'spor-salonu'],
            ['name' => 'Sahil', 'slug' => 'sahil'],
            ['name' => 'Plaj', 'slug' => 'plaj'],
            ['name' => 'Restoran', 'slug' => 'restoran'],
            ['name' => 'Kafe', 'slug' => 'kafe'],
        ]);

        // ============================================
        // 4️⃣ ULAŞIM (Transportation)
        // ============================================
        $ulasimData = [
            'name' => 'Ulaşım',
            'slug' => 'ulasim',
            'description' => 'Ulaşım imkanları ve yakınlık',
            'icon' => 'fas fa-bus',
            // ✅ Context7: 'type' field'ı migration'da yok, kaldırıldı
            'display_order' => 40,
        ];

        if ($hasStatusColumn) {
            $ulasimData['status'] = true;
        } elseif ($hasEnabledColumn) {
            $ulasimData['enabled'] = true;
        }

        $ulasim = FeatureCategory::updateOrCreate(
            ['slug' => 'ulasim'],
            $ulasimData
        );
        $this->command->info('✅ Ulaşım kategorisi oluşturuldu');

        // Ulaşım Özellikleri
        $this->createFeatures($ulasim, [
            ['name' => 'Metro', 'slug' => 'metro'],
            ['name' => 'Otobüs', 'slug' => 'otobus'],
            ['name' => 'Tramvay', 'slug' => 'tramvay'],
            ['name' => 'İstasyon', 'slug' => 'istasyon'],
            ['name' => 'Havalimanı', 'slug' => 'havalimani'],
            ['name' => 'Otogar', 'slug' => 'otogar'],
            ['name' => 'Ana Cadde', 'slug' => 'ana-cadde'],
            ['name' => 'Sahil Yolu', 'slug' => 'sahil-yolu'],
        ]);

        // ============================================
        // 5️⃣ CEPHE (Facade/Frontage)
        // ============================================
        $cepheData = [
            'name' => 'Cephe',
            'slug' => 'cephe',
            'description' => 'Bina cephesi ve yön bilgisi',
            'icon' => 'fas fa-compass',
            // ✅ Context7: 'type' field'ı migration'da yok, kaldırıldı
            'display_order' => 50,
        ];

        if ($hasStatusColumn) {
            $cepheData['status'] = true;
        } elseif ($hasEnabledColumn) {
            $cepheData['enabled'] = true;
        }

        $cephe = FeatureCategory::updateOrCreate(
            ['slug' => 'cephe'],
            $cepheData
        );
        $this->command->info('✅ Cephe kategorisi oluşturuldu');

        // Cephe Özellikleri
        $this->createFeatures($cephe, [
            ['name' => 'Kuzey', 'slug' => 'kuzey'],
            ['name' => 'Güney', 'slug' => 'guney'],
            ['name' => 'Doğu', 'slug' => 'dogu'],
            ['name' => 'Batı', 'slug' => 'bati'],
            ['name' => 'Güneydoğu', 'slug' => 'guneydogu'],
            ['name' => 'Güneybatı', 'slug' => 'guneybati'],
            ['name' => 'Kuzeydoğu', 'slug' => 'kuzeydogu'],
            ['name' => 'Kuzeybatı', 'slug' => 'kuzeybati'],
            ['name' => 'Yola Cepheli', 'slug' => 'yola-cepheli'],
            ['name' => 'Cadde Cepheli', 'slug' => 'cadde-cepheli'],
        ]);

        // ============================================
        // 6️⃣ MANZARA (View/Scenery)
        // ============================================
        $manzaraData = [
            'name' => 'Manzara',
            'slug' => 'manzara',
            'description' => 'Manzara ve görünüm',
            'icon' => 'fas fa-mountain',
            // ✅ Context7: 'type' field'ı migration'da yok, kaldırıldı
            'display_order' => 60,
        ];

        if ($hasStatusColumn) {
            $manzaraData['status'] = true;
        } elseif ($hasEnabledColumn) {
            $manzaraData['enabled'] = true;
        }

        $manzara = FeatureCategory::updateOrCreate(
            ['slug' => 'manzara'],
            $manzaraData
        );
        $this->command->info('✅ Manzara kategorisi oluşturuldu');

        // Manzara Özellikleri
        $this->createFeatures($manzara, [
            ['name' => 'Boğaz', 'slug' => 'bogaz'],
            ['name' => 'Cadde', 'slug' => 'cadde'],
            ['name' => 'Dağ', 'slug' => 'dag'],
            ['name' => 'Deniz', 'slug' => 'deniz'],
            ['name' => 'Doğa', 'slug' => 'doga'],
            ['name' => 'Göl', 'slug' => 'gol'],
            ['name' => 'Havuz', 'slug' => 'havuz'],
            ['name' => 'Nehir', 'slug' => 'nehir'],
            ['name' => 'Park', 'slug' => 'park-manzara'],
            ['name' => 'Şehir', 'slug' => 'sehir'],
            ['name' => 'Vadi', 'slug' => 'vadi'],
            ['name' => 'Yeşil Alan', 'slug' => 'yesil-alan'],
        ]);

        $this->command->info('');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✅ Revy.com.tr tarzı özellik kategorileri oluşturuldu!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');

        // Özet
        $revyCategories = FeatureCategory::whereIn('slug', [
            'ic-ozellikleri',
            'dis-ozellikleri',
            'muhit',
            'ulasim',
            'cephe',
            'manzara'
        ])->count();

        $revyFeatures = Feature::whereHas('category', function ($query) {
            $query->whereIn('slug', [
                'ic-ozellikleri',
                'dis-ozellikleri',
                'muhit',
                'ulasim',
                'cephe',
                'manzara'
            ]);
        })->count();

        $this->command->info("📊 Özet:");
        $this->command->info("   - Revy Kategorileri: {$revyCategories}");
        $this->command->info("   - Revy Özellikleri: {$revyFeatures}");
    }

    /**
     * Helper: Özellikleri toplu oluştur
     */
    private function createFeatures(FeatureCategory $category, array $features): void
    {
        // Context7: Schema kontrolü
        $hasStatusColumn = Schema::hasColumn('features', 'status');
        $hasEnabledColumn = Schema::hasColumn('features', 'enabled');

        foreach ($features as $index => $feature) {
            $featureData = [
                'name' => $feature['name'],
                'slug' => $feature['slug'],
                'feature_category_id' => $category->id,
                'type' => 'checkbox', // Revy tarzı checkbox özellikler
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'display_order' => $index + 1,
            ];

            // Context7: status/enabled kolonu varsa ekle
            if ($hasStatusColumn) {
                $featureData['status'] = true;
            } elseif ($hasEnabledColumn) {
                $featureData['enabled'] = true;
            }

            Feature::updateOrCreate(
                ['slug' => $feature['slug']],
                $featureData
            );
        }

        $count = count($features);
        $this->command->info("   └─ {$count} özellik eklendi");
    }
}
