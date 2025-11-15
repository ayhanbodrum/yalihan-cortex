<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;
use App\Models\Feature;
use App\Models\FeatureCategory;
use App\Models\FeatureAssignment;

/**
 * Proje Kategori Özellik İlişkilendirme Seeder
 *
 * Projeler kategorisi ve Satılık yayın tipi için tüm proje özelliklerini ilişkilendirir.
 */
class ProjeOzellikIliskilendirmeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔗 Proje Özellik İlişkilendirmeleri oluşturuluyor...');

        // Context7: Schema kontrolü
        if (!Schema::hasTable('feature_assignments')) {
            $this->command->warn('⚠️ feature_assignments tablosu bulunamadı!');
            return;
        }

        // Projeler kategori ve yayın tipini bul
        $projeler = IlanKategori::find(5); // Projeler

        if (!$projeler) {
            $this->command->warn('⚠️ Projeler kategorisi bulunamadı!');
            return;
        }

        // Satılık yayın tipini bul
        $satilik = IlanKategoriYayinTipi::where('kategori_id', 5)
            ->where('yayin_tipi', 'Satılık')
            ->first();

        if (!$satilik) {
            $this->command->warn('⚠️ Satılık yayın tipi bulunamadı!');
            return;
        }

        $this->command->info("  ✓ Projeler Kategori ID: {$projeler->id}");
        $this->command->info("  ✓ Satılık Yayın Tipi ID: {$satilik->id}");

        // Proje ile ilgili tüm özellik kategorilerini bul
        $projeKategorileri = FeatureCategory::where(function($q) {
                $q->where('name', 'like', '%Proje%')
                  ->orWhere('name', 'like', '%Site%')
                  ->orWhere('name', 'like', '%Bina%')
                  ->orWhere('name', 'like', '%Konut%')
                  ->orWhere('name', 'like', '%İnşaat%');
            })
            ->with(['features' => function($q) {
                $hasStatusColumn = Schema::hasColumn('features', 'status');
                $hasEnabledColumn = Schema::hasColumn('features', 'enabled');

                if ($hasStatusColumn) {
                    $q->where('status', true);
                } elseif ($hasEnabledColumn) {
                    $q->where('enabled', true);
                }

                $q->orderBy('display_order');
            }])
            ->get();

        $toplamAtanan = 0;
        $order = 1;

        foreach ($projeKategorileri as $kategori) {
            $this->command->info("  📋 {$kategori->name} kategorisi işleniyor...");

            foreach ($kategori->features as $feature) {
                // Özelliği yayın tipine ata (polymorphic relationship)
                try {
                    FeatureAssignment::updateOrCreate(
                        [
                            'feature_id' => $feature->id,
                            'assignable_type' => IlanKategoriYayinTipi::class,
                            'assignable_id' => $satilik->id,
                        ],
                        [
                            'is_required' => false,
                            'is_visible' => true,
                            'display_order' => $order,
                            'group_name' => $kategori->name,
                        ]
                    );
                    $toplamAtanan++;
                    $order++;
                } catch (\Exception $e) {
                    $this->command->warn("    ⚠️ {$feature->name} atanamadı: " . $e->getMessage());
                }
            }

            $this->command->info("    ✓ {$kategori->name}: {$kategori->features->count()} özellik atandı");
        }

        $this->command->info("✅ Toplam {$toplamAtanan} özellik projeler satılık yayın tipine atandı!");
    }
}
