<?php

namespace Database\Seeders;

use App\Models\IlanKategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * İlan Yayın Tipi Seeder
 *
 * Context7 standartlarına uygun yayın tiplerini seed eder.
 * Context7 Standardı: C7-YAYIN-TIPI-SEEDER-2025-11-05
 *
 * Yayın Tipleri:
 * - Satılık (Tüm kategoriler için)
 * - Kiralık (Tüm kategoriler için)
 * - Günlük Kiralık (Yazlık için)
 * - Haftalık Kiralık (Yazlık için)
 * - Aylık Kiralık (Yazlık için)
 */
class IlanYayinTipiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📢 İlan Yayın Tipi Seeder başlatılıyor...');
        $this->command->info('📋 Context7 Standardı: C7-YAYIN-TIPI-SEEDER-2025-11-05');
        $this->command->newLine();

        // Context7: Schema kontrolü
        if (! Schema::hasTable('ilan_kategori_yayin_tipleri')) {
            $this->command->warn('   ⚠️ ilan_kategori_yayin_tipleri tablosu yok!');

            return;
        }

        $hasStatusColumn = Schema::hasColumn('ilan_kategori_yayin_tipleri', 'status');

        // Ana kategorileri bul
        $konut = IlanKategori::where('slug', 'konut')->first();
        $arsa = IlanKategori::where('slug', 'arsa')->first();
        $isyeri = IlanKategori::where('slug', 'isyeri')->first();
        $turistikTesis = IlanKategori::where('slug', 'turistik-tesis')->first();
        $yazlik = IlanKategori::where('slug', 'yazlik')->first();

        if (! $konut || ! $arsa || ! $isyeri) {
            $this->command->warn('   ⚠️ Ana kategoriler bulunamadı! Önce CompleteIlanKategoriSeeder çalıştırın.');

            return;
        }

        $count = 0;

        // Tüm kategoriler için Satılık/Kiralık
        $kategoriler = [
            ['kategori' => $konut, 'tipler' => ['Satılık', 'Kiralık']],
            ['kategori' => $arsa, 'tipler' => ['Satılık', 'Kiralık']],
            ['kategori' => $isyeri, 'tipler' => ['Satılık', 'Kiralık', 'Devren Satılık', 'Devren Kiralık']],
            ['kategori' => $turistikTesis, 'tipler' => ['Satılık', 'Kiralık']],
        ];

        foreach ($kategoriler as $kategoriData) {
            $order = 1;
            foreach ($kategoriData['tipler'] as $tip) {
                $data = [
                    'kategori_id' => $kategoriData['kategori']->id,
                    'yayin_tipi' => $tip,
                    'display_order' => $order++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($hasStatusColumn) {
                    $data['status'] = true;
                }

                DB::table('ilan_kategori_yayin_tipleri')->updateOrInsert(
                    ['kategori_id' => $kategoriData['kategori']->id, 'yayin_tipi' => $tip],
                    $data
                );
                $count++;
            }
        }

        // Yazlık için özel tipler
        if ($yazlik) {
            $yazlikTipleri = [
                'Günlük Kiralık',
                'Haftalık Kiralık',
                'Aylık Kiralık',
                'Sezonluk Kiralık',
            ];

            $order = 1;
            foreach ($yazlikTipleri as $tip) {
                $data = [
                    'kategori_id' => $yazlik->id,
                    'yayin_tipi' => $tip,
                    'display_order' => $order++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($hasStatusColumn) {
                    $data['status'] = true;
                }

                DB::table('ilan_kategori_yayin_tipleri')->updateOrInsert(
                    ['kategori_id' => $yazlik->id, 'yayin_tipi' => $tip],
                    $data
                );
                $count++;
            }
        }

        $this->command->info("   ✓ {$count} yayın tipi eklendi/güncellendi");
        $this->command->info('✅ İlan Yayın Tipi Seeder tamamlandı!');
    }
}
