<?php

namespace Database\Seeders;

use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use Illuminate\Database\Seeder;

class BodrumMahallelerSeeder extends Seeder
{
    /**
     * Run the database seeder.
     * Bodrum mahalleleri - Context7 uyumlu
     */
    public function run(): void
    {
        // Muğla ilini bul
        $mugla = Il::where('il_adi', 'Muğla')->first();

        if (! $mugla) {
            $this->command->error('❌ Muğla ili bulunamadı!');

            return;
        }

        // Bodrum ilçesini bul veya oluştur
        $bodrum = Ilce::firstOrCreate(
            ['il_id' => $mugla->id, 'ilce_adi' => 'Bodrum']
        );

        $this->command->info("✅ Bodrum ilçesi: ID #{$bodrum->id}");

        // Bodrum Mahalleleri (Nüfusa göre sıralı)
        $mahalleler = [
            ['mahalle_adi' => 'Bitez', 'nufus' => 10116],
            ['mahalle_adi' => 'Yalıkavak', 'nufus' => 5901],
            ['mahalle_adi' => 'Turgutreis', 'nufus' => 5308],
            ['mahalle_adi' => 'Bahçelievler', 'nufus' => 4989],
            ['mahalle_adi' => 'Kumbahçe', 'nufus' => 4918],
            ['mahalle_adi' => 'Cevat Şakir', 'nufus' => 4778],
            ['mahalle_adi' => 'Eskiçeşme', 'nufus' => 4574],
            ['mahalle_adi' => 'Konacık', 'nufus' => 4245],
            ['mahalle_adi' => 'Akyarlar', 'nufus' => 4150],
            ['mahalle_adi' => 'Gündoğan', 'nufus' => 3857],
            ['mahalle_adi' => 'Yokuşbaşı', 'nufus' => 3816],
            ['mahalle_adi' => 'İslamhaneleri', 'nufus' => 3564],
            ['mahalle_adi' => 'Gümüşlük', 'nufus' => 3519],
            ['mahalle_adi' => 'Yeniköy', 'nufus' => 3292],
            ['mahalle_adi' => 'Çarşı', 'nufus' => 3114],
            ['mahalle_adi' => 'Gümbet', 'nufus' => 2782],
            ['mahalle_adi' => 'Torba', 'nufus' => 2228],
            ['mahalle_adi' => 'Cumhuriyet', 'nufus' => 2227],
            ['mahalle_adi' => 'Peksimet', 'nufus' => 2010],
            ['mahalle_adi' => 'Yakaköy', 'nufus' => 2003],
            ['mahalle_adi' => 'Türkkuyusu', 'nufus' => 1616],
            ['mahalle_adi' => 'Güvercinlik', 'nufus' => 1416],
            ['mahalle_adi' => 'Mumcular', 'nufus' => 1272],
            ['mahalle_adi' => 'Mazıköy', 'nufus' => 1087],
            ['mahalle_adi' => 'Dereköy', 'nufus' => 1065],
            ['mahalle_adi' => 'Tepecik', 'nufus' => 999],
            ['mahalle_adi' => 'Pınarlıbelen', 'nufus' => 928],
            ['mahalle_adi' => 'Bahçeyaka', 'nufus' => 798],
            ['mahalle_adi' => 'Yeniköy (Karaova)', 'nufus' => 762],
            ['mahalle_adi' => 'Gürece', 'nufus' => 669],
            ['mahalle_adi' => 'Sazköy', 'nufus' => 639],
            ['mahalle_adi' => 'Dağbelen', 'nufus' => 601],
            ['mahalle_adi' => 'Çamlık', 'nufus' => 400],
            ['mahalle_adi' => 'Çamarası', 'nufus' => 399],
            ['mahalle_adi' => 'Kemer', 'nufus' => 383],
            ['mahalle_adi' => 'Kumköy', 'nufus' => 344],
            ['mahalle_adi' => 'Tepecik (Karaova)', 'nufus' => 330],
            ['mahalle_adi' => 'Gökpınar', 'nufus' => 203],

            // Küçük veya boş nüfuslu mahalleler
            ['mahalle_adi' => 'Türkbükü', 'nufus' => 0],
            ['mahalle_adi' => 'Kızılağaç', 'nufus' => 0],
            ['mahalle_adi' => 'Gölköy', 'nufus' => 0],
            ['mahalle_adi' => 'Çırkan', 'nufus' => 0],
            ['mahalle_adi' => 'Dirmil', 'nufus' => 0],
            ['mahalle_adi' => 'Karaova', 'nufus' => 0],
            ['mahalle_adi' => 'Çömlekçi', 'nufus' => 0],
            ['mahalle_adi' => 'Karabağ', 'nufus' => 0],
            ['mahalle_adi' => 'Geriş', 'nufus' => 0],
            ['mahalle_adi' => 'Gölbaşı', 'nufus' => 0],
            ['mahalle_adi' => 'Farilya', 'nufus' => 0],
            ['mahalle_adi' => 'Koyunbaba', 'nufus' => 0],
            ['mahalle_adi' => 'Müskebi', 'nufus' => 0],
            ['mahalle_adi' => 'Yahşi', 'nufus' => 0],
            ['mahalle_adi' => 'Akçaalan', 'nufus' => 0],
            ['mahalle_adi' => 'Umurca', 'nufus' => 0],
            ['mahalle_adi' => 'Küçükbük', 'nufus' => 0],
            ['mahalle_adi' => 'Çiftlik', 'nufus' => 0],
        ];

        $this->command->info('📍 Bodrum mahalleleri ekleniyor...');

        $eklenenSayisi = 0;
        $mevcutSayisi = 0;

        foreach ($mahalleler as $mahalleData) {
            $mahalle = Mahalle::firstOrCreate(
                [
                    'ilce_id' => $bodrum->id,
                    'mahalle_adi' => $mahalleData['mahalle_adi'],
                ],
                [
                    'mahalle_kodu' => null,
                    'posta_kodu' => null,
                ]
            );

            if ($mahalle->wasRecentlyCreated) {
                $eklenenSayisi++;
                $this->command->line("  ✅ {$mahalleData['mahalle_adi']} (Nüfus: {$mahalleData['nufus']})");
            } else {
                $mevcutSayisi++;
            }
        }

        $this->command->newLine();
        $this->command->info('📊 İşlem Özeti:');
        $this->command->table(
            ['Durum', 'Sayı'],
            [
                ['Yeni Eklenen', $eklenenSayisi],
                ['Zaten Mevcut', $mevcutSayisi],
                ['Toplam', count($mahalleler)],
            ]
        );

        $this->command->newLine();
        $this->command->info('🎉 Bodrum mahalleleri başarıyla yüklendi!');
        $this->command->line("   📍 İlçe: Bodrum (ID: {$bodrum->id})");
        $this->command->line('   🏘️  Mahalle: '.count($mahalleler).' adet');

        // Test için örnek mahalle göster
        $this->command->newLine();
        $this->command->info('🧪 Test Mahallesi:');
        $dirmil = Mahalle::where('ilce_id', $bodrum->id)
            ->where('mahalle_adi', 'Dirmil Mahallesi')
            ->first();

        if ($dirmil) {
            $this->command->line("   ✅ Dirmil Mahallesi (ID: {$dirmil->id})");
            $this->command->line("   📍 API: /api/location/neighborhoods/{$bodrum->id}");
        }
    }
}
