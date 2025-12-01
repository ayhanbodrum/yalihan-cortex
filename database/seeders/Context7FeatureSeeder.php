<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Context7 Feature Seeder
 *
 * Context7 standartlarına uygun özellik sistemi.
 * Tüm eski özellik seeder'larından verileri birleştirir.
 *
 * Context7 Standardı: C7-FEATURE-SEEDER-2025-09-13
 * Versiyon: 4.0.0
 */
class Context7FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('⚙️ Context7 Özellik Sistemi oluşturuluyor...');

        // 1. Temel özellikler oluştur
        $this->createBasicFeatures();

        // 2. Konut özellikleri oluştur
        $this->createHousingFeatures();

        // 3. İş yeri özellikleri oluştur
        $this->createCommercialFeatures();

        // 4. Arsa özellikleri oluştur
        $this->createLandFeatures();

        // 5. Turistik tesis özellikleri oluştur
        $this->createTourismFeatures();

        $this->command->info('✅ Context7 özellik sistemi başarıyla oluşturuldu!');
    }

    /**
     * Temel özellikler oluştur
     */
    private function createBasicFeatures(): void
    {
        $this->command->info('🔧 Temel özellikler oluşturuluyor...');

        $basicFeatures = [
            [
                'category_id' => 1, // Konut
                'slug' => 'fiyat',
                'name' => 'Fiyat',
                'description' => 'İlan fiyatı',
                'is_filterable' => true,
                'show_on_card' => true,
                'display_order' => 1,
                'data_type' => 'decimal',
                'is_required' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1, // Konut
                'slug' => 'metrekare',
                'name' => 'Metrekare',
                'description' => 'Toplam metrekare',
                'is_filterable' => true,
                'show_on_card' => true,
                'display_order' => 2,
                'data_type' => 'integer',
                'is_required' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1, // Konut
                'slug' => 'oda-sayisi',
                'name' => 'Oda Sayısı',
                'description' => 'Oda sayısı (1+1, 2+1, 3+1, vb.)',
                'is_filterable' => true,
                'show_on_card' => true,
                'display_order' => 3,
                'data_type' => 'string',
                'is_required' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1, // Konut
                'slug' => 'banyo-sayisi',
                'name' => 'Banyo Sayısı',
                'description' => 'Banyo ve tuvalet sayısı',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 4,
                'data_type' => 'integer',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1, // Konut
                'slug' => 'balkon-sayisi',
                'name' => 'Balkon Sayısı',
                'description' => 'Balkon ve teras sayısı',
                'is_filterable' => false,
                'show_on_card' => false,
                'display_order' => 5,
                'data_type' => 'integer',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($basicFeatures as $feature) {
            $this->createFeature($feature);
        }

        $this->command->info('✅ Temel özellikler oluşturuldu');
    }

    /**
     * Konut özellikleri oluştur
     */
    private function createHousingFeatures(): void
    {
        $this->command->info('🏠 Konut özellikleri oluşturuluyor...');

        $housingFeatures = [
            [
                'category_id' => 1, // Konut
                'slug' => 'bina-yasi',
                'name' => 'Bina Yaşı',
                'description' => 'Binanın yaşı (yıl)',
                'is_filterable' => true,
                'show_on_card' => true,
                'display_order' => 6,
                'data_type' => 'integer',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1, // Konut
                'slug' => 'isitma-tipi',
                'name' => 'Isıtma Tipi',
                'description' => 'Isıtma sistemi (kombi, merkezi, vb.)',
                'is_filterable' => true,
                'show_on_card' => true,
                'display_order' => 7,
                'data_type' => 'string',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1, // Konut
                'slug' => 'kat',
                'name' => 'Kat',
                'description' => 'Bulunduğu kat',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 8,
                'data_type' => 'string',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1, // Konut
                'slug' => 'toplam-kat',
                'name' => 'Toplam Kat',
                'description' => 'Binanın toplam kat sayısı',
                'is_filterable' => false,
                'show_on_card' => false,
                'display_order' => 9,
                'data_type' => 'integer',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1, // Konut
                'slug' => 'yapim-yili',
                'name' => 'Yapım Yılı',
                'description' => 'Binanın yapım yılı',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 10,
                'data_type' => 'integer',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1, // Konut
                'slug' => 'asansor',
                'name' => 'Asansör',
                'description' => 'Asansör var mı?',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 11,
                'data_type' => 'boolean',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1, // Konut
                'slug' => 'otopark',
                'name' => 'Otopark',
                'description' => 'Otopark var mı?',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 12,
                'data_type' => 'boolean',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1, // Konut
                'slug' => 'guvenlik',
                'name' => 'Güvenlik',
                'description' => 'Güvenlik sistemi var mı?',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 13,
                'data_type' => 'boolean',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($housingFeatures as $feature) {
            $this->createFeature($feature);
        }

        $this->command->info('✅ Konut özellikleri oluşturuldu');
    }

    /**
     * İş yeri özellikleri oluştur
     */
    private function createCommercialFeatures(): void
    {
        $this->command->info('🏢 İş yeri özellikleri oluşturuluyor...');

        $commercialFeatures = [
            [
                'category_id' => 2, // İş Yeri
                'slug' => 'kat-sayisi',
                'name' => 'Kat Sayısı',
                'description' => 'İş yerinin kat sayısı',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 1,
                'data_type' => 'integer',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2, // İş Yeri
                'slug' => 'vitrin',
                'name' => 'Vitrin',
                'description' => 'Vitrin var mı?',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 2,
                'data_type' => 'boolean',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2, // İş Yeri
                'slug' => 'klima',
                'name' => 'Klima',
                'description' => 'Klima sistemi var mı?',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 3,
                'data_type' => 'boolean',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2, // İş Yeri
                'slug' => 'internet',
                'name' => 'İnternet',
                'description' => 'İnternet bağlantısı var mı?',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 4,
                'data_type' => 'boolean',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($commercialFeatures as $feature) {
            $this->createFeature($feature);
        }

        $this->command->info('✅ İş yeri özellikleri oluşturuldu');
    }

    /**
     * Arsa özellikleri oluştur
     */
    private function createLandFeatures(): void
    {
        $this->command->info('🗺️ Arsa özellikleri oluşturuluyor...');

        $landFeatures = [
            [
                'category_id' => 3, // Arsa
                'slug' => 'imar-statusu',
                'name' => 'İmar Durumu',
                'description' => 'İmar planındaki statusu',
                'is_filterable' => true,
                'show_on_card' => true,
                'display_order' => 1,
                'data_type' => 'string',
                'is_required' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 3, // Arsa
                'slug' => 'elektrik',
                'name' => 'Elektrik',
                'description' => 'Elektrik bağlantısı var mı?',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 2,
                'data_type' => 'boolean',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 3, // Arsa
                'slug' => 'su',
                'name' => 'Su',
                'description' => 'Su bağlantısı var mı?',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 3,
                'data_type' => 'boolean',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 3, // Arsa
                'slug' => 'yol',
                'name' => 'Yol',
                'description' => 'Yol bağlantısı var mı?',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 4,
                'data_type' => 'boolean',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($landFeatures as $feature) {
            $this->createFeature($feature);
        }

        $this->command->info('✅ Arsa özellikleri oluşturuldu');
    }

    /**
     * Turistik tesis özellikleri oluştur
     */
    private function createTourismFeatures(): void
    {
        $this->command->info('🏖️ Turistik tesis özellikleri oluşturuluyor...');

        $tourismFeatures = [
            [
                'category_id' => 4, // Turistik Tesis
                'slug' => 'oda-sayisi',
                'name' => 'Oda Sayısı',
                'description' => 'Toplam oda sayısı',
                'is_filterable' => true,
                'show_on_card' => true,
                'display_order' => 1,
                'data_type' => 'integer',
                'is_required' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 4, // Turistik Tesis
                'slug' => 'havuz',
                'name' => 'Havuz',
                'description' => 'Havuz var mı?',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 2,
                'data_type' => 'boolean',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 4, // Turistik Tesis
                'slug' => 'restoran',
                'name' => 'Restoran',
                'description' => 'Restoran var mı?',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 3,
                'data_type' => 'boolean',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 4, // Turistik Tesis
                'slug' => 'spa',
                'name' => 'Spa',
                'description' => 'Spa ve wellness merkezi var mı?',
                'is_filterable' => true,
                'show_on_card' => false,
                'display_order' => 4,
                'data_type' => 'boolean',
                'is_required' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($tourismFeatures as $feature) {
            $this->createFeature($feature);
        }

        $this->command->info('✅ Turistik tesis özellikleri oluşturuldu');
    }

    /**
     * Özellik oluştur ve çevirisini ekle
     */
    private function createFeature(array $featureData): void
    {
        $name = $featureData['name'];
        $description = $featureData['description'];
        $slug = $featureData['slug'];

        // name ve description'ı features tablosuna ekle
        $featureData['name'] = $name;
        $featureData['description'] = $description;

        // Özellik zaten var mı kontrol et
        $existingFeature = DB::table('features')->where('slug', $slug)->first();

        if (! $existingFeature) {
            $featureId = DB::table('features')->insertGetId($featureData);

            // Özellik çevirisini ekle
            DB::table('feature_translations')->insert([
                'feature_id' => $featureId,
                'locale' => 'tr',
                'name' => $name,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
