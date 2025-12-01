<?php

namespace Database\Seeders;

use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Muğla-Aydın Lokasyon Seeder
 *
 * Context7 standartlarına uygun Muğla ve Aydın bölgesi lokasyon verilerini seed eder.
 * Context7 Standardı: C7-LOCATION-SEEDER-2025-11-05
 *
 * Kapsam:
 * - Muğla: 13 ilçe + popüler mahalleler (Bodrum, Marmaris, Fethiye, vb.)
 * - Aydın: 17 ilçe + popüler mahalleler (Didim, Kuşadası, Söke, vb.)
 */
class MuglaAydinLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📍 Muğla-Aydın Lokasyon Seeder başlatılıyor...');
        $this->command->info('📋 Context7 Standardı: C7-LOCATION-SEEDER-2025-11-05');
        $this->command->newLine();

        // Context7: Schema kontrolü
        $hasStatusColumn = Schema::hasColumn('iller', 'status');
        $hasIlceStatusColumn = Schema::hasColumn('ilceler', 'status');
        $hasMahalleStatusColumn = Schema::hasColumn('mahalleler', 'status');

        // 1. Muğla İli
        $this->command->info('🏛️ 1. Muğla ili kontrol ediliyor...');
        $mugla = Il::where('plaka_kodu', '48')->first();

        if (! $mugla) {
            $this->command->warn('   ⚠️ Muğla ili bulunamadı! Önce TurkiyeIlleriSeeder çalıştırın.');

            return;
        }

        $this->command->info("   ✓ Muğla ili bulundu (ID: {$mugla->id})");

        // 2. Muğla İlçeleri
        $this->command->info('🏘️ 2. Muğla ilçeleri seed ediliyor...');
        $muglaIlceleri = $this->getMuglaIlceleri();
        $muglaIlceCount = 0;

        foreach ($muglaIlceleri as $ilceData) {
            $data = [
                'il_id' => $mugla->id,
                'ilce_adi' => $ilceData['ilce_adi'],
                'ilce_kodu' => $ilceData['ilce_kodu'],
            ];

            if ($hasIlceStatusColumn) {
                $data['status'] = true;
            }

            Ilce::updateOrCreate(
                ['il_id' => $mugla->id, 'ilce_adi' => $ilceData['ilce_adi']],
                $data
            );
            $muglaIlceCount++;
        }

        $this->command->info("   ✓ {$muglaIlceCount} ilçe eklendi/güncellendi (Muğla)");

        // 3. Muğla Mahalleleri (Popüler bölgeler)
        $this->command->info('🏘️ 3. Muğla mahalleleri seed ediliyor...');
        $muglaMahalleCount = $this->seedMuglaMahalleleri($mugla, $hasMahalleStatusColumn);
        $this->command->info("   ✓ {$muglaMahalleCount} mahalle eklendi/güncellendi (Muğla)");

        // 4. Aydın İli
        $this->command->info('🏛️ 4. Aydın ili kontrol ediliyor...');
        $aydin = Il::where('plaka_kodu', '09')->first();

        if (! $aydin) {
            $this->command->warn('   ⚠️ Aydın ili bulunamadı! Önce TurkiyeIlleriSeeder çalıştırın.');

            return;
        }

        $this->command->info("   ✓ Aydın ili bulundu (ID: {$aydin->id})");

        // 5. Aydın İlçeleri
        $this->command->info('🏘️ 5. Aydın ilçeleri seed ediliyor...');
        $aydinIlceleri = $this->getAydinIlceleri();
        $aydinIlceCount = 0;

        foreach ($aydinIlceleri as $ilceData) {
            $data = [
                'il_id' => $aydin->id,
                'ilce_adi' => $ilceData['ilce_adi'],
                'ilce_kodu' => $ilceData['ilce_kodu'],
            ];

            if ($hasIlceStatusColumn) {
                $data['status'] = true;
            }

            Ilce::updateOrCreate(
                ['il_id' => $aydin->id, 'ilce_adi' => $ilceData['ilce_adi']],
                $data
            );
            $aydinIlceCount++;
        }

        $this->command->info("   ✓ {$aydinIlceCount} ilçe eklendi/güncellendi (Aydın)");

        // 6. Aydın Mahalleleri (Popüler bölgeler)
        $this->command->info('🏘️ 6. Aydın mahalleleri seed ediliyor...');
        $aydinMahalleCount = $this->seedAydinMahalleleri($aydin, $hasMahalleStatusColumn);
        $this->command->info("   ✓ {$aydinMahalleCount} mahalle eklendi/güncellendi (Aydın)");

        $this->command->newLine();
        $this->command->info('✅ Muğla-Aydın Lokasyon Seeder tamamlandı!');
    }

    /**
     * Muğla ilçeleri listesi
     */
    private function getMuglaIlceleri(): array
    {
        return [
            ['ilce_adi' => 'Bodrum', 'ilce_kodu' => '4801'],
            ['ilce_adi' => 'Milas', 'ilce_kodu' => '4802'],
            ['ilce_adi' => 'Fethiye', 'ilce_kodu' => '4803'],
            ['ilce_adi' => 'Marmaris', 'ilce_kodu' => '4804'],
            ['ilce_adi' => 'Datça', 'ilce_kodu' => '4805'],
            ['ilce_adi' => 'Köyceğiz', 'ilce_kodu' => '4806'],
            ['ilce_adi' => 'Ula', 'ilce_kodu' => '4807'],
            ['ilce_adi' => 'Yatağan', 'ilce_kodu' => '4808'],
            ['ilce_adi' => 'Ortaca', 'ilce_kodu' => '4809'],
            ['ilce_adi' => 'Dalaman', 'ilce_kodu' => '4810'],
            ['ilce_adi' => 'Seydikemer', 'ilce_kodu' => '4811'],
            ['ilce_adi' => 'Kavaklıdere', 'ilce_kodu' => '4812'],
            ['ilce_adi' => 'Menteşe', 'ilce_kodu' => '4813'],
        ];
    }

    /**
     * Aydın ilçeleri listesi
     */
    private function getAydinIlceleri(): array
    {
        return [
            ['ilce_adi' => 'Merkez', 'ilce_kodu' => '0901'],
            ['ilce_adi' => 'Didim', 'ilce_kodu' => '0902'],
            ['ilce_adi' => 'Kuşadası', 'ilce_kodu' => '0903'],
            ['ilce_adi' => 'Söke', 'ilce_kodu' => '0904'],
            ['ilce_adi' => 'Nazilli', 'ilce_kodu' => '0905'],
            ['ilce_adi' => 'Efeler', 'ilce_kodu' => '0906'],
            ['ilce_adi' => 'Germencik', 'ilce_kodu' => '0907'],
            ['ilce_adi' => 'Bozdoğan', 'ilce_kodu' => '0908'],
            ['ilce_adi' => 'İncirliova', 'ilce_kodu' => '0909'],
            ['ilce_adi' => 'Köşk', 'ilce_kodu' => '0910'],
            ['ilce_adi' => 'Kuyucak', 'ilce_kodu' => '0911'],
            ['ilce_adi' => 'Çine', 'ilce_kodu' => '0912'],
            ['ilce_adi' => 'Sultanhisar', 'ilce_kodu' => '0913'],
            ['ilce_adi' => 'Yenipazar', 'ilce_kodu' => '0914'],
            ['ilce_adi' => 'Karacasu', 'ilce_kodu' => '0915'],
            ['ilce_adi' => 'Karpuzlu', 'ilce_kodu' => '0916'],
            ['ilce_adi' => 'Koçarlı', 'ilce_kodu' => '0917'],
        ];
    }

    /**
     * Muğla mahallelerini seed et
     */
    private function seedMuglaMahalleleri(Il $mugla, bool $hasStatusColumn): int
    {
        $count = 0;
        $mahalleler = [
            // Bodrum
            ['ilce_adi' => 'Bodrum', 'mahalleler' => [
                'Bitez', 'Yalıkavak', 'Turgutreis', 'Gümbet', 'Ortakent', 'Gündoğan',
                'Türkbükü', 'Gölköy', 'Torba', 'Kadıkalesi', 'Çamlık', 'Konacık',
                'Çiftlik', 'Mumcular', 'Karaova', 'Mazıköy', 'Kızılağaç', 'Akçaalan',
            ]],
            // Marmaris
            ['ilce_adi' => 'Marmaris', 'mahalleler' => [
                'Marmaris Merkez', 'İçmeler', 'Siteler', 'Armutalan', 'Beldibi',
                'Turunç', 'Bozburun', 'Selimiye', 'Orhaniye', 'Turgut',
            ]],
            // Fethiye
            ['ilce_adi' => 'Fethiye', 'mahalleler' => [
                'Fethiye Merkez', 'Çalış', 'Ölüdeniz', 'Hisarönü', 'Ovacık',
                'Kalkan', 'Kaş', 'Patara', 'Kaya Köyü', 'Telmessos',
            ]],
            // Datça
            ['ilce_adi' => 'Datça', 'mahalleler' => [
                'Datça Merkez', 'Eski Datça', 'Kızlan', 'Mesudiye', 'Reşadiye',
            ]],
            // Milas
            ['ilce_adi' => 'Milas', 'mahalleler' => [
                'Milas Merkez', 'Güllük', 'Ören', 'Selimiye', 'Bafa',
            ]],
        ];

        foreach ($mahalleler as $ilceData) {
            $ilce = Ilce::where('il_id', $mugla->id)
                ->where('ilce_adi', $ilceData['ilce_adi'])
                ->first();

            if (! $ilce) {
                continue;
            }

            foreach ($ilceData['mahalleler'] as $mahalleAdi) {
                $data = [
                    'ilce_id' => $ilce->id,
                    'mahalle_adi' => $mahalleAdi,
                ];

                if ($hasStatusColumn) {
                    $data['status'] = true;
                }

                Mahalle::updateOrCreate(
                    ['ilce_id' => $ilce->id, 'mahalle_adi' => $mahalleAdi],
                    $data
                );
                $count++;
            }
        }

        return $count;
    }

    /**
     * Aydın mahallelerini seed et
     */
    private function seedAydinMahalleleri(Il $aydin, bool $hasStatusColumn): int
    {
        $count = 0;
        $mahalleler = [
            // Didim
            ['ilce_adi' => 'Didim', 'mahalleler' => [
                'Altınkum', 'Akbük', 'Mavişehir', 'Yeşilkent', 'Tavşanburnu',
                'Didim Merkez', 'Akyeniköy', 'Akköy', 'Balat', 'Boyalık',
            ]],
            // Kuşadası
            ['ilce_adi' => 'Kuşadası', 'mahalleler' => [
                'Kuşadası Merkez', 'Davutlar', 'Güzelçamlı', 'Soğucak', 'Kadıkalesi',
                'Kadınlar Denizi', 'Yılancı Burnu', 'Pamucak', 'Kaleiçi', 'Çamlimanı',
            ]],
            // Söke
            ['ilce_adi' => 'Söke', 'mahalleler' => [
                'Söke Merkez', 'Güllübahçe', 'Pamukyaka', 'Bağarası', 'Tuzburgazı',
            ]],
            // Efeler
            ['ilce_adi' => 'Efeler', 'mahalleler' => [
                'Efeler Merkez', 'Kurtuluş', 'Adnan Menderes', 'Cumhuriyet', 'Zafer',
            ]],
        ];

        foreach ($mahalleler as $ilceData) {
            $ilce = Ilce::where('il_id', $aydin->id)
                ->where('ilce_adi', $ilceData['ilce_adi'])
                ->first();

            if (! $ilce) {
                continue;
            }

            foreach ($ilceData['mahalleler'] as $mahalleAdi) {
                $data = [
                    'ilce_id' => $ilce->id,
                    'mahalle_adi' => $mahalleAdi,
                ];

                if ($hasStatusColumn) {
                    $data['status'] = true;
                }

                Mahalle::updateOrCreate(
                    ['ilce_id' => $ilce->id, 'mahalle_adi' => $mahalleAdi],
                    $data
                );
                $count++;
            }
        }

        return $count;
    }
}
