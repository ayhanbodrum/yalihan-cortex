<?php

namespace Database\Seeders;

use App\Models\FeatureAssignment;
use App\Models\FeatureCategory;
use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Yazlık Kiralık Ana Kategori Özellik İlişkilendirme Seeder
 *
 * Yazlık Kiralık ana kategorisinin tüm yayın tipleri (Günlük, Haftalık, Sezonluk)
 * için Yazlık villa özelliklerini ilişkilendirir.
 */
class YazlikKiralikOzellikIliskilendirmeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔗 Yazlık Kiralık Özellik İlişkilendirmeleri oluşturuluyor...');

        // Context7: Schema kontrolü
        if (! Schema::hasTable('feature_assignments')) {
            $this->command->warn('⚠️ feature_assignments tablosu bulunamadı!');

            return;
        }

        // Yazlık Kiralık ana kategoriyi bul
        $yazlikKiralik = IlanKategori::where('name', 'Yazlık Kiralık')->where('seviye', 0)->first();

        if (! $yazlikKiralik) {
            $this->command->warn('⚠️ Yazlık Kiralık ana kategorisi bulunamadı! Önce YazlikKiralikAnaKategoriSeeder çalıştırın.');

            return;
        }

        // Yayın tiplerini bul (Günlük, Haftalık, Sezonluk)
        $yayinTipleri = IlanKategoriYayinTipi::where('kategori_id', $yazlikKiralik->id)
            ->whereIn('yayin_tipi', ['Günlük', 'Haftalık', 'Sezonluk'])
            ->get();

        if ($yayinTipleri->isEmpty()) {
            $this->command->warn('⚠️ Yazlık Kiralık yayın tipleri bulunamadı!');

            return;
        }

        $this->command->info("  ✓ Yazlık Kiralık Ana Kategori ID: {$yazlikKiralik->id}");
        $this->command->info("  ✓ Yayın Tipi Sayısı: {$yayinTipleri->count()}");

        // Yazlık ile ilgili tüm özellik kategorilerini bul
        $yazlikKategorileri = FeatureCategory::where(function ($q) {
            $q->where('name', 'like', '%Dış Mekan%')
                ->orWhere('name', 'like', '%İç Mekan%')
                ->orWhere('name', 'like', '%Yatak Odası%')
                ->orWhere('name', 'like', '%Banyo%')
                ->orWhere('name', 'like', '%Ek Hizmet%')
                ->orWhere('name', 'like', '%Ulaşım%')
                ->orWhere('name', 'like', '%Eğlence%')
                ->orWhere('name', 'like', '%Güvenlik%')
                ->orWhere('name', 'like', '%Çocuk%')
                ->orWhere('name', 'like', '%Evcil%')
                ->orWhere('name', 'like', '%Havuz Detay%');
        })
            ->with(['features' => function ($q) {
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

        if ($yazlikKategorileri->isEmpty()) {
            $this->command->warn('⚠️ Yazlık özellik kategorileri bulunamadı! Önce YazlikVillaOzellikleriSeeder çalıştırın.');

            return;
        }

        $toplamAtanan = 0;

        // Her yayın tipi için özellikleri ata
        foreach ($yayinTipleri as $yayinTipi) {
            $this->command->info("  📢 {$yayinTipi->yayin_tipi} yayın tipi için özellikler atanıyor...");
            $order = 1;
            $yayinTipiAtanan = 0;

            foreach ($yazlikKategorileri as $kategori) {
                foreach ($kategori->features as $feature) {
                    try {
                        FeatureAssignment::updateOrCreate(
                            [
                                'feature_id' => $feature->id,
                                'assignable_type' => IlanKategoriYayinTipi::class,
                                'assignable_id' => $yayinTipi->id,
                            ],
                            [
                                'is_required' => false,
                                'is_visible' => true,
                                'display_order' => $order,
                                'group_name' => $kategori->name,
                            ]
                        );
                        $yayinTipiAtanan++;
                        $order++;
                    } catch (\Exception $e) {
                        $this->command->warn("    ⚠️ {$feature->name} atanamadı: ".$e->getMessage());
                    }
                }
            }

            $this->command->info("    ✓ {$yayinTipi->yayin_tipi}: {$yayinTipiAtanan} özellik atandı");
            $toplamAtanan += $yayinTipiAtanan;
        }

        $this->command->info("✅ Toplam {$toplamAtanan} özellik Yazlık Kiralık yayın tiplerine atandı!");
        $this->command->info("   📊 {$yayinTipleri->count()} yayın tipi × {$yazlikKategorileri->sum(fn ($c) => $c->features->count())} özellik = {$toplamAtanan} atama");
    }
}
