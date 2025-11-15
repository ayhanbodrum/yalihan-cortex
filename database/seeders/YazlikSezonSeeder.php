<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Yazlık Sezon Seeder
 *
 * Context7 standartlarına uygun yazlık sezon tanımlarını seed eder.
 * Context7 Standardı: C7-YAZLIK-SEASON-SEEDER-2025-11-05
 *
 * Sezonlar:
 * - Yaz Sezonu (Yüksek): Haziran-Ağustos
 * - Ara Sezon (Orta): Nisan-Mayıs, Eylül-Ekim
 * - Kış Sezonu (Düşük): Kasım-Mart
 */
class YazlikSezonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📅 Yazlık Sezon Seeder başlatılıyor...');
        $this->command->info('📋 Context7 Standardı: C7-YAZLIK-SEASON-SEEDER-2025-11-05');
        $this->command->newLine();

        // Context7: Schema kontrolü
        if (!Schema::hasTable('ilan_takvim_sezonlar')) {
            $this->command->warn('   ⚠️ ilan_takvim_sezonlar tablosu yok!');
            return;
        }

        $hasStatusColumn = Schema::hasColumn('ilan_takvim_sezonlar', 'status');

        // Sezon verileri
        $sezonlar = [
            [
                'sezon_adi' => 'Yaz Sezonu (Yüksek)',
                'baslangic_tarihi' => '2025-06-01',
                'bitis_tarihi' => '2025-08-31',
                'fiyat_carpani' => 1.5,
            ],
            [
                'sezon_adi' => 'Ara Sezon (İlkbahar)',
                'baslangic_tarihi' => '2025-04-01',
                'bitis_tarihi' => '2025-05-31',
                'fiyat_carpani' => 1.2,
            ],
            [
                'sezon_adi' => 'Ara Sezon (Sonbahar)',
                'baslangic_tarihi' => '2025-09-01',
                'bitis_tarihi' => '2025-10-31',
                'fiyat_carpani' => 1.2,
            ],
            [
                'sezon_adi' => 'Kış Sezonu (Düşük)',
                'baslangic_tarihi' => '2025-11-01',
                'bitis_tarihi' => '2026-03-31',
                'fiyat_carpani' => 0.8,
            ],
        ];

        $count = 0;
        foreach ($sezonlar as $sezon) {
            $data = [
                'sezon_adi' => $sezon['sezon_adi'],
                'baslangic_tarihi' => $sezon['baslangic_tarihi'],
                'bitis_tarihi' => $sezon['bitis_tarihi'],
                'fiyat_carpani' => $sezon['fiyat_carpani'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Context7: status kolonu varsa ekle
            if ($hasStatusColumn) {
                $data['status'] = true;
            }

            DB::table('ilan_takvim_sezonlar')->updateOrInsert(
                ['sezon_adi' => $sezon['sezon_adi']],
                $data
            );
            $count++;
        }

        $this->command->info("   ✓ {$count} sezon eklendi/güncellendi");
        $this->command->info('✅ Yazlık Sezon Seeder tamamlandı!');
    }
}
