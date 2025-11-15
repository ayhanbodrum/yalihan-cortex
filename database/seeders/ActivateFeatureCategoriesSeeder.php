<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\FeatureCategory;
use App\Models\IlanKategoriYayinTipi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivateFeatureCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Özellik Kategorilerini Aktif Ediyorum...');

        DB::beginTransaction();

        try {
            // 1. Tüm kategorileri aktif et
            $updated = FeatureCategory::where('enabled', false)->update(['enabled' => true]);
            $this->command->info("   ✅ {$updated} kategori aktif edildi");

            // 2. Tüm özellikleri aktif et
            $featuresUpdated = Feature::where('enabled', false)->update(['enabled' => true]);
            $this->command->info("   ✅ {$featuresUpdated} özellik aktif edildi");

            // 3. Property Type'lara default özellikler ata
            $this->assignDefaultFeatures();

            DB::commit();

            $this->command->info('✅ İşlem başarıyla tamamlandı!');
            $this->printStatistics();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Hata: ' . $e->getMessage());
            $this->command->error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Property Type'lara default özellikler ata
     */
    private function assignDefaultFeatures(): void
    {
        $this->command->info('📦 Property Type ilişkilendirmeleri yapılıyor...');

        // ID bazlı ilişkilendirme (slug kolonu olmadığı için)
        $propertyTypes = IlanKategoriYayinTipi::whereIn('id', [1, 2, 3, 4])->get();

        $this->command->info("   🔍 " . $propertyTypes->count() . " Property Type bulundu");

        foreach ($propertyTypes as $propertyType) {
            // Property Type ID'sine göre category belirle
            $categorySlug = match ($propertyType->id) {
                1 => 'konut-ozellikleri', // Konut
                2 => 'arsa-ozellikleri',  // Arsa
                3 => 'isyeri-ozellikleri', // İşyeri
                4 => 'yazlik-ozellikleri', // Yazlık
                default => null
            };

            if (!$categorySlug) {
                continue;
            }

            $features = Feature::whereHas('category', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug);
            })->get();

            if ($features->isEmpty()) {
                $this->command->warn("   ⚠️  {$categorySlug} için özellik bulunamadı");
                continue;
            }

            foreach ($features as $index => $feature) {
                $propertyType->featureAssignments()->firstOrCreate(
                    ['feature_id' => $feature->id],
                    [
                        'is_required' => $index < 2, // İlk 2 özellik zorunlu
                        'is_visible' => true,
                        'display_order' => $index,
                        'group_name' => $index < 4 ? 'Genel Bilgiler' : 'Özellikler'
                    ]
                );
            }

            $this->command->info("   ✅ Property Type #{$propertyType->id}: " . $features->count() . " özellik atandı");
        }
    }

    /**
     * Print statistics
     */
    private function printStatistics(): void
    {
        $this->command->info("\n📊 GÜNCEL DURUM:");
        $this->command->table(
            ['Tablo', 'Toplam', 'Aktif', 'Pasif'],
            [
                [
                    'Feature Categories',
                    FeatureCategory::count(),
                    FeatureCategory::where('enabled', true)->count(),
                    FeatureCategory::where('enabled', false)->count()
                ],
                [
                    'Features',
                    Feature::count(),
                    Feature::where('enabled', true)->count(),
                    Feature::where('enabled', false)->count()
                ],
                [
                    'Feature Assignments',
                    DB::table('feature_assignments')->count(),
                    DB::table('feature_assignments')->where('is_visible', true)->count(),
                    DB::table('feature_assignments')->where('is_visible', false)->count()
                ]
            ]
        );
    }
}
