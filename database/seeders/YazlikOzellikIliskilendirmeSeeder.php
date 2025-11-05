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
 * Yazlık Kategori Özellik İlişkilendirme Seeder
 * 
 * Yazlık kategorisi ve Kiralık yayın tipi için tüm özellikleri ilişkilendirir.
 */
class YazlikOzellikIliskilendirmeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔗 Yazlık Özellik İlişkilendirmeleri oluşturuluyor...');

        // Context7: Schema kontrolü
        if (!Schema::hasTable('feature_assignments')) {
            $this->command->warn('⚠️ feature_assignments tablosu bulunamadı!');
            return;
        }

        // Yazlık kategori ve yayın tipini bul
        $yazlik = IlanKategori::where('name', 'Yazlık')->where('seviye', 1)->first();
        
        if (!$yazlik) {
            $this->command->warn('⚠️ Yazlık kategorisi bulunamadı!');
            return;
        }

        // Yazlık'ın parent'ı (Konut - ID: 1)
        $konutKategoriId = $yazlik->parent_id;
        
        // Kiralık yayın tipini bul
        $kiralik = IlanKategoriYayinTipi::where('kategori_id', $konutKategoriId)
            ->where('yayin_tipi', 'Kiralık')
            ->first();

        if (!$kiralik) {
            $this->command->warn('⚠️ Kiralık yayın tipi bulunamadı!');
            return;
        }

        $this->command->info("  ✓ Yazlık Kategori ID: {$yazlik->id}");
        $this->command->info("  ✓ Kiralık Yayın Tipi ID: {$kiralik->id}");

        // Yazlık ile ilgili tüm özellik kategorilerini bul
        $yazlikKategorileri = FeatureCategory::where(function($q) {
                $q->where('name', 'like', '%Dış%')
                  ->orWhere('name', 'like', '%İç%')
                  ->orWhere('name', 'like', '%Yatak%')
                  ->orWhere('name', 'like', '%Banyo%')
                  ->orWhere('name', 'like', '%Ek Hizmet%')
                  ->orWhere('name', 'like', '%Ulaşım%')
                  ->orWhere('name', 'like', '%Eğlence%')
                  ->orWhere('name', 'like', '%Güvenlik%')
                  ->orWhere('name', 'like', '%Çocuk%')
                  ->orWhere('name', 'like', '%Evcil%')
                  ->orWhere('name', 'like', '%Havuz Detay%');
            })
            ->with(['features' => function($q) {
                $hasStatusColumn = Schema::hasColumn('features', 'status');
                $hasEnabledColumn = Schema::hasColumn('features', 'enabled');
                
                if ($hasStatusColumn) {
                    $q->where('status', true);
                } elseif ($hasEnabledColumn) {
                    $q->where('enabled', true);
                }
                
                $q->orderBy('order');
            }])
            ->get();

        $toplamAtanan = 0;
        $order = 1;

        foreach ($yazlikKategorileri as $kategori) {
            $this->command->info("  📋 {$kategori->name} kategorisi işleniyor...");
            
            foreach ($kategori->features as $feature) {
                // Özelliği yayın tipine ata (polymorphic relationship)
                try {
                    FeatureAssignment::updateOrCreate(
                        [
                            'feature_id' => $feature->id,
                            'assignable_type' => IlanKategoriYayinTipi::class,
                            'assignable_id' => $kiralik->id,
                        ],
                        [
                            'is_required' => false,
                            'is_visible' => true,
                            'order' => $order,
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

        $this->command->info("✅ Toplam {$toplamAtanan} özellik yazlık kiralama yayın tipine atandı!");
    }
}

