<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\FeatureCategory;
use Illuminate\Database\Seeder;

class SampleFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎯 Örnek özellikler oluşturuluyor...');

        // KONUT ÖZELLİKLERİ
        $this->createKonutFeatures();

        // İŞYERİ ÖZELLİKLERİ
        $this->createIsyeriFeatures();

        // ARSA ÖZELLİKLERİ (zaten var, güncellenecek)
        $this->updateArsaFeatures();

        // YAZLIK ÖZELLİKLERİ
        $this->createYazlikFeatures();

        $this->command->info('✅ Örnek özellikler başarıyla oluşturuldu!');
        $this->printStatistics();
    }

    private function createKonutFeatures()
    {
        $this->command->info('🏠 Konut özellikleri oluşturuluyor...');

        $category = FeatureCategory::firstOrCreate(
            ['slug' => 'konut-ozellikleri'],
            [
                'name' => 'Konut Özellikleri',
                'type' => 'konut',
                'description' => 'Konutlara özel alanlar (daire, villa, vb.)',
                'icon' => '🏠',
                'display_order' => 10,
                'enabled' => true,
            ]
        );

        $features = [
            // Genel Bilgiler
            ['name' => 'Oda Sayısı', 'slug' => 'oda-sayisi', 'field_type' => 'select', 'field_icon' => '🛏️', 'field_options' => ['1+0', '1+1', '2+1', '3+1', '4+1', '5+1', '6+1'], 'group' => 'Genel Bilgiler'],
            ['name' => 'Banyo Sayısı', 'slug' => 'banyo-sayisi', 'field_type' => 'number', 'field_icon' => '🚿', 'field_unit' => 'adet', 'group' => 'Genel Bilgiler'],
            ['name' => 'Brüt m²', 'slug' => 'brut-m2', 'field_type' => 'number', 'field_icon' => '📏', 'field_unit' => 'm²', 'is_required' => true, 'group' => 'Genel Bilgiler'],
            ['name' => 'Net m²', 'slug' => 'net-m2', 'field_type' => 'number', 'field_icon' => '📐', 'field_unit' => 'm²', 'group' => 'Genel Bilgiler'],
            ['name' => 'Kat', 'slug' => 'kat', 'field_type' => 'select', 'field_icon' => '🏢', 'field_options' => ['Bodrum', 'Zemin', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10+'], 'group' => 'Genel Bilgiler'],
            ['name' => 'Bina Yaşı', 'slug' => 'bina-yasi', 'field_type' => 'number', 'field_icon' => '📅', 'field_unit' => 'yıl', 'group' => 'Genel Bilgiler'],

            // Bina Özellikleri
            ['name' => 'Kat Sayısı', 'slug' => 'kat-sayisi', 'field_type' => 'number', 'field_icon' => '🏛️', 'field_unit' => 'kat', 'group' => 'Bina Özellikleri'],
            ['name' => 'Isıtma Tipi', 'slug' => 'isitma-tipi', 'field_type' => 'select', 'field_icon' => '🔥', 'field_options' => ['Doğalgaz', 'Kombi', 'Merkezi', 'Klima', 'Soba', 'Kat Kaloriferi'], 'group' => 'Bina Özellikleri'],
            ['name' => 'Balkon', 'slug' => 'balkon', 'field_type' => 'checkbox', 'field_icon' => '🌿', 'group' => 'Bina Özellikleri'],
            ['name' => 'Asansör', 'slug' => 'asansor', 'field_type' => 'checkbox', 'field_icon' => '🛗', 'group' => 'Bina Özellikleri'],
            ['name' => 'Otopark', 'slug' => 'otopark', 'field_type' => 'checkbox', 'field_icon' => '🅿️', 'group' => 'Bina Özellikleri'],

            // Güvenlik
            ['name' => 'Güvenlik', 'slug' => 'guvenlik', 'field_type' => 'checkbox', 'field_icon' => '🔒', 'group' => 'Güvenlik'],
            ['name' => 'Kamera Sistemi', 'slug' => 'kamera-sistemi', 'field_type' => 'checkbox', 'field_icon' => '📹', 'group' => 'Güvenlik'],
            ['name' => 'Alarm Sistemi', 'slug' => 'alarm-sistemi', 'field_type' => 'checkbox', 'field_icon' => '🚨', 'group' => 'Güvenlik'],
        ];

        $count = count($features);
        $this->createFeatures($category, $features);
        $this->command->info("   ✅ {$count} konut özelliği oluşturuldu");
    }

    private function createIsyeriFeatures()
    {
        $this->command->info('🏢 İşyeri özellikleri oluşturuluyor...');

        $category = FeatureCategory::firstOrCreate(
            ['slug' => 'isyeri-ozellikleri'],
            [
                'name' => 'İşyeri Özellikleri',
                'type' => 'ticari',
                'description' => 'İşyerlerine özel alanlar (dükkan, ofis, fabrika)',
                'icon' => '🏢',
                'display_order' => 20,
                'enabled' => true,
            ]
        );

        $features = [
            // Genel Bilgiler
            ['name' => 'Alan', 'slug' => 'alan', 'field_type' => 'number', 'field_icon' => '📏', 'field_unit' => 'm²', 'is_required' => true, 'group' => 'Genel Bilgiler'],
            ['name' => 'Kat', 'slug' => 'isyeri-kat', 'field_type' => 'select', 'field_icon' => '🏢', 'field_options' => ['Bodrum', 'Zemin', '1', '2', '3', '4', '5+'], 'group' => 'Genel Bilgiler'],
            ['name' => 'Cephe Sayısı', 'slug' => 'cephe-sayisi', 'field_type' => 'number', 'field_icon' => '🏛️', 'field_unit' => 'adet', 'group' => 'Genel Bilgiler'],
            ['name' => 'Ön Cephe', 'slug' => 'on-cephe', 'field_type' => 'checkbox', 'field_icon' => '🏪', 'group' => 'Genel Bilgiler'],

            // Teknik Özellikler
            ['name' => 'Tavan Yüksekliği', 'slug' => 'tavan-yuksekligi', 'field_type' => 'number', 'field_icon' => '📐', 'field_unit' => 'm', 'group' => 'Teknik Özellikler'],
            ['name' => 'Elektrik Gücü', 'slug' => 'elektrik-gucu', 'field_type' => 'number', 'field_icon' => '⚡', 'field_unit' => 'kW', 'group' => 'Teknik Özellikler'],
            ['name' => 'Jeneratör', 'slug' => 'jenerator', 'field_type' => 'checkbox', 'field_icon' => '🔌', 'group' => 'Teknik Özellikler'],
            ['name' => 'Klima', 'slug' => 'klima', 'field_type' => 'checkbox', 'field_icon' => '❄️', 'group' => 'Teknik Özellikler'],

            // İmkanlar
            ['name' => 'Otopark', 'slug' => 'isyeri-otopark', 'field_type' => 'checkbox', 'field_icon' => '🅿️', 'group' => 'İmkanlar'],
            ['name' => 'Asansör', 'slug' => 'isyeri-asansor', 'field_type' => 'checkbox', 'field_icon' => '🛗', 'group' => 'İmkanlar'],
            ['name' => 'Mutfak', 'slug' => 'mutfak', 'field_type' => 'checkbox', 'field_icon' => '🍳', 'group' => 'İmkanlar'],
            ['name' => 'Tuvalet', 'slug' => 'tuvalet', 'field_type' => 'checkbox', 'field_icon' => '🚽', 'group' => 'İmkanlar'],
        ];

        $count = count($features);
        $this->createFeatures($category, $features);
        $this->command->info("   ✅ {$count} işyeri özelliği oluşturuldu");
    }

    private function updateArsaFeatures()
    {
        $this->command->info('🏗️ Arsa özellikleri güncelleniyor...');

        $category = FeatureCategory::where('slug', 'arsa-ozellikleri')->first();

        if (! $category) {
            $this->command->warn('   ⚠️ Arsa kategorisi bulunamadı, oluşturuluyor...');
            $category = FeatureCategory::create([
                'name' => 'Arsa Özellikleri',
                'type' => 'arsa',
                'description' => 'Arsaya özel alanlar',
                'icon' => '🏗️',
                'display_order' => 30,
                'enabled' => true,
            ]);
        }

        // Mevcut arsa özellikleri zaten var, sadece eksikleri ekle
        $additionalFeatures = [
            ['name' => 'Arsa Alan', 'slug' => 'arsa-alan', 'field_type' => 'number', 'field_icon' => '📏', 'field_unit' => 'm²', 'is_required' => true],
            ['name' => 'Tapu Durumu', 'slug' => 'tapu-durumu', 'field_type' => 'select', 'field_icon' => '📜', 'field_options' => ['Kat İrtifaklı', 'Kat Mülkiyetli', 'Arsa', 'Tarla']],
        ];

        foreach ($additionalFeatures as $index => $featureData) {
            Feature::firstOrCreate(
                ['slug' => $featureData['slug']],
                array_merge($featureData, [
                    'category_id' => $category->id,
                    'display_order' => 100 + $index,
                    'enabled' => true,
                ])
            );
        }

        $this->command->info('   ✅ '.count($additionalFeatures).' arsa özelliği eklendi');
    }

    private function createYazlikFeatures()
    {
        $this->command->info('🏖️ Yazlık özellikleri oluşturuluyor...');

        $category = FeatureCategory::firstOrCreate(
            ['slug' => 'yazlik-ozellikleri'],
            [
                'name' => 'Yazlık Özellikleri',
                'type' => 'yazlik',
                'description' => 'Yazlıklara özel alanlar',
                'icon' => '🏖️',
                'display_order' => 40,
                'enabled' => true,
            ]
        );

        $features = [
            // Temel Bilgiler
            ['name' => 'Oda Sayısı', 'slug' => 'yazlik-oda-sayisi', 'field_type' => 'select', 'field_icon' => '🛏️', 'field_options' => ['1+0', '1+1', '2+1', '3+1', '4+1', '5+1'], 'group' => 'Temel Bilgiler'],
            ['name' => 'Yatak Kapasitesi', 'slug' => 'yatak-kapasitesi', 'field_type' => 'number', 'field_icon' => '🛌', 'field_unit' => 'kişi', 'group' => 'Temel Bilgiler'],
            ['name' => 'Alan', 'slug' => 'yazlik-alan', 'field_type' => 'number', 'field_icon' => '📏', 'field_unit' => 'm²', 'group' => 'Temel Bilgiler'],

            // Amenities
            ['name' => 'Havuz', 'slug' => 'havuz', 'field_type' => 'checkbox', 'field_icon' => '🏊', 'group' => 'Amenities', 'is_filterable' => true],
            ['name' => 'Jakuzi', 'slug' => 'jakuzi', 'field_type' => 'checkbox', 'field_icon' => '🛁', 'group' => 'Amenities'],
            ['name' => 'Sauna', 'slug' => 'sauna', 'field_type' => 'checkbox', 'field_icon' => '🧖', 'group' => 'Amenities'],
            ['name' => 'Denize Uzaklık', 'slug' => 'denize-uzaklik', 'field_type' => 'number', 'field_icon' => '🌊', 'field_unit' => 'm', 'group' => 'Konum'],
            ['name' => 'Deniz Manzarası', 'slug' => 'deniz-manzarasi', 'field_type' => 'checkbox', 'field_icon' => '🌅', 'group' => 'Konum'],
            ['name' => 'Bahçe', 'slug' => 'bahce', 'field_type' => 'checkbox', 'field_icon' => '🌳', 'group' => 'Dış Mekan'],
            ['name' => 'Teras', 'slug' => 'teras', 'field_type' => 'checkbox', 'field_icon' => '🏡', 'group' => 'Dış Mekan'],
        ];

        $count = count($features);
        $this->createFeatures($category, $features);
        $this->command->info("   ✅ {$count} yazlık özelliği oluşturuldu");
    }

    private function createFeatures(FeatureCategory $category, array $features)
    {
        foreach ($features as $index => $featureData) {
            // field_options varsa JSON encode et
            if (isset($featureData['field_options']) && is_array($featureData['field_options'])) {
                $featureData['field_options'] = json_encode($featureData['field_options']);
            }

            // group_name'i group'dan al ve sil
            $groupName = $featureData['group'] ?? null;
            unset($featureData['group']);

            Feature::firstOrCreate(
                ['slug' => $featureData['slug']],
                array_merge($featureData, [
                    'category_id' => $category->id,
                    'display_order' => $index,
                    'enabled' => true,
                    'is_filterable' => $featureData['is_filterable'] ?? true,
                    'is_searchable' => false,
                    'show_in_listing' => true,
                    'show_in_detail' => true,
                    'show_in_filter' => $featureData['is_filterable'] ?? true,
                ])
            );
        }
    }

    private function printStatistics()
    {
        $this->command->info("\n📊 FEATURE İSTATİSTİKLERİ:");
        $this->command->table(
            ['Kategori', 'Özellik Sayısı'],
            [
                ['Konut Özellikleri', Feature::whereHas('category', fn ($q) => $q->where('slug', 'konut-ozellikleri'))->count()],
                ['İşyeri Özellikleri', Feature::whereHas('category', fn ($q) => $q->where('slug', 'isyeri-ozellikleri'))->count()],
                ['Arsa Özellikleri', Feature::whereHas('category', fn ($q) => $q->where('slug', 'arsa-ozellikleri'))->count()],
                ['Yazlık Özellikleri', Feature::whereHas('category', fn ($q) => $q->where('slug', 'yazlik-ozellikleri'))->count()],
                ['TOPLAM', Feature::count()],
            ]
        );

        $this->command->info("\n📈 KATEGORİ İSTATİSTİKLERİ:");
        $this->command->table(
            ['Alan', 'Değer'],
            [
                ['Toplam Kategori', FeatureCategory::count()],
                ['Aktif Kategori', FeatureCategory::where('enabled', true)->count()],
                ['Toplam Özellik', Feature::count()],
                ['Aktif Özellik', Feature::where('enabled', true)->count()],
            ]
        );
    }
}
