<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\FeatureCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PolymorphicFeaturesMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Polymorphic Features Migration başlıyor...');

        DB::beginTransaction();

        try {
            // 1. Migrate Özellik Kategorileri → Feature Categories
            $this->migrateOzellikKategorileri();

            // 2. Migrate Özellikler → Features
            $this->migrateOzellikler();

            // 3. Migrate Site Özellikleri → Features
            $this->migrateSiteOzellikleri();

            // 4. Create default Property Type Features
            $this->createPropertyTypeFeatures();

            DB::commit();

            $this->command->info('✅ Migration başarıyla tamamlandı!');
            $this->printStatistics();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Migration hatası: '.$e->getMessage());
            $this->command->error('Stack trace: '.$e->getTraceAsString());
        }
    }

    /**
     * Migrate ozellik_kategorileri table
     */
    private function migrateOzellikKategorileri(): void
    {
        $this->command->info('📦 Özellik Kategorileri migrate ediliyor...');

        $kategoriler = DB::table('ozellik_kategorileri')->get();

        foreach ($kategoriler as $kategori) {
            FeatureCategory::create([
                'name' => $kategori->name,
                'slug' => $kategori->slug ?? Str::slug($kategori->name),
                'type' => $this->mapApplicationArea($kategori->application_area ?? null),
                'description' => $kategori->description ?? null,
                'icon' => $kategori->icon ?? null,
                'display_order' => $kategori->order ?? 0,
                'enabled' => $kategori->enabled ?? true,
                'created_at' => $kategori->created_at ?? now(),
                'updated_at' => $kategori->updated_at ?? now(),
            ]);
        }

        $this->command->info("   ✅ {$kategoriler->count()} kategori migrate edildi");
    }

    /**
     * Migrate ozellikler table
     */
    private function migrateOzellikler(): void
    {
        $this->command->info('📦 Özellikler migrate ediliyor...');

        $ozellikler = DB::table('ozellikler')->get();

        foreach ($ozellikler as $ozellik) {
            // Find category
            $categoryId = null;
            if (! empty($ozellik->kategori_id)) {
                $oldKategori = DB::table('ozellik_kategorileri')
                    ->where('id', $ozellik->kategori_id)
                    ->first();
                if ($oldKategori) {
                    $newCategory = FeatureCategory::where('slug', $oldKategori->slug ?? Str::slug($oldKategori->name))->first();
                    $categoryId = $newCategory?->id;
                }
            }

            Feature::create([
                'category_id' => $categoryId,
                'name' => $ozellik->name,
                'slug' => $ozellik->slug ?? Str::slug($ozellik->name),
                'description' => $ozellik->description ?? null,
                'field_type' => $this->mapFieldType($ozellik->type ?? 'text'),
                'field_options' => ! empty($ozellik->options) ? json_decode($ozellik->options, true) : null,
                'field_unit' => $ozellik->unit ?? null,
                'field_icon' => $ozellik->icon ?? null,
                'is_required' => $ozellik->is_required ?? false,
                'is_filterable' => $ozellik->is_filterable ?? true,
                'is_searchable' => $ozellik->is_searchable ?? false,
                'enabled' => $ozellik->enabled ?? true,
                'display_order' => $ozellik->order ?? 0,
                'show_in_listing' => true,
                'show_in_detail' => true,
                'show_in_filter' => $ozellik->is_filterable ?? true,
                'created_at' => $ozellik->created_at ?? now(),
                'updated_at' => $ozellik->updated_at ?? now(),
            ]);
        }

        $this->command->info("   ✅ {$ozellikler->count()} özellik migrate edildi");
    }

    /**
     * Migrate site_ozellikleri table
     */
    private function migrateSiteOzellikleri(): void
    {
        $this->command->info('📦 Site Özellikleri migrate ediliyor...');

        if (! DB::getSchemaBuilder()->hasTable('site_ozellikleri')) {
            $this->command->warn('   ⚠️ site_ozellikleri tablosu bulunamadı');

            return;
        }

        $siteOzellikleri = DB::table('site_ozellikleri')->get();

        // Create "Site Özellikleri" category if not exists
        $siteCategory = FeatureCategory::firstOrCreate(
            ['slug' => 'site-ozellikleri'],
            [
                'name' => 'Site Özellikleri',
                'type' => 'konut',
                'description' => 'Konut sitelerinin genel özellikleri',
                'icon' => 'building',
                'display_order' => 100,
                'enabled' => true,
            ]
        );

        foreach ($siteOzellikleri as $ozellik) {
            Feature::create([
                'category_id' => $siteCategory->id,
                'name' => $ozellik->name,
                'slug' => $ozellik->slug ?? Str::slug($ozellik->name),
                'description' => $ozellik->description ?? null,
                'field_type' => 'checkbox',
                'field_icon' => $ozellik->icon ?? null,
                'is_required' => false,
                'is_filterable' => true,
                'enabled' => $ozellik->enabled ?? true,
                'display_order' => $ozellik->order ?? 0,
                'show_in_listing' => true,
                'show_in_detail' => true,
                'show_in_filter' => true,
                'created_at' => $ozellik->created_at ?? now(),
                'updated_at' => $ozellik->updated_at ?? now(),
            ]);
        }

        $this->command->info("   ✅ {$siteOzellikleri->count()} site özelliği migrate edildi");
    }

    /**
     * Create default features for Property Types (Arsa, Konut, etc.)
     */
    private function createPropertyTypeFeatures(): void
    {
        $this->command->info('📦 Property Type özellikleri oluşturuluyor...');

        // Arsa Özellikleri
        $arsaCategory = FeatureCategory::firstOrCreate(
            ['slug' => 'arsa-ozellikleri'],
            [
                'name' => 'Arsa Özellikleri',
                'type' => 'arsa',
                'description' => 'Arsaya özel alanlar',
                'icon' => 'map',
                'display_order' => 1,
                'enabled' => true,
            ]
        );

        $arsaFeatures = [
            ['name' => 'Ada No', 'slug' => 'ada-no', 'field_type' => 'text', 'is_required' => true],
            ['name' => 'Parsel No', 'slug' => 'parsel-no', 'field_type' => 'text', 'is_required' => true],
            ['name' => 'İmar Durumu', 'slug' => 'imar-durumu', 'field_type' => 'select', 'field_options' => json_encode(['İmarlı', 'İmarsız', 'Ticari İmar', 'Konut İmarlı'])],
            ['name' => 'KAKS', 'slug' => 'kaks', 'field_type' => 'number', 'field_unit' => '%'],
            ['name' => 'TAKS', 'slug' => 'taks', 'field_type' => 'number', 'field_unit' => '%'],
            ['name' => 'Gabari', 'slug' => 'gabari', 'field_type' => 'number', 'field_unit' => 'm'],
        ];

        $featureCount = 0;
        foreach ($arsaFeatures as $index => $feature) {
            Feature::firstOrCreate(
                ['slug' => $feature['slug']],
                array_merge($feature, [
                    'category_id' => $arsaCategory->id,
                    'display_order' => $index,
                    'enabled' => true,
                ])
            );
            $featureCount++;
        }

        $this->command->info("   ✅ {$featureCount} arsa özelliği oluşturuldu");
    }

    /**
     * Map application_area to type
     */
    private function mapApplicationArea($area): ?string
    {
        return match ($area) {
            'konut', 'Konut' => 'konut',
            'arsa', 'Arsa' => 'arsa',
            'yazlik', 'Yazlık' => 'yazlik',
            'isyeri', 'İşyeri' => 'ticari',
            default => null,
        };
    }

    /**
     * Map field type
     */
    private function mapFieldType($type): string
    {
        return match ($type) {
            'boolean', 'yes_no' => 'checkbox',
            'text', 'string' => 'text',
            'number', 'integer', 'float' => 'number',
            'select', 'dropdown' => 'select',
            'radio' => 'radio',
            'checkbox' => 'checkbox',
            'textarea' => 'textarea',
            default => 'text',
        };
    }

    /**
     * Print migration statistics
     */
    private function printStatistics(): void
    {
        $this->command->info("\n📊 MIGRATION İSTATİSTİKLERİ:");
        $this->command->table(
            ['Tablo', 'Kayıt Sayısı'],
            [
                ['Feature Categories', FeatureCategory::count()],
                ['Features', Feature::count()],
            ]
        );
    }
}
