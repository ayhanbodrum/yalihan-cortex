<?php

namespace Database\Seeders;

use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use Illuminate\Database\Seeder;

class YataganMahallelerSeeder extends Seeder
{
    /**
     * Yatağan mahalleleri - Standart format (Mahallesi kelimesi yok)
     */
    public function run(): void
    {
        // Muğla ilini bul
        $mugla = Il::where('il_adi', 'Muğla')->first();

        if (!$mugla) {
            $this->command->error('❌ Muğla ili bulunamadı!');
            return;
        }

        // Yatağan ilçesini bul veya oluştur
        $yatagan = Ilce::firstOrCreate(
            ['il_id' => $mugla->id, 'ilce_adi' => 'Yatağan']
        );

        $this->command->info("✅ Yatağan ilçesi: ID #{$yatagan->id}");

        // Yatağan Mahalleleri (Alfabetik sıralı)
        $mahalleler = [
            'Akçakese',
            'Allahdiyen',
            'Başçayır',
            'Bozarmut',
            'Bozyer',
            'Çamköy',
            'Eskihisar',
            'Gökçeyaka',
            'Işıklar',
            'Karaçulha',
            'Kayadibi',
            'Kazıklıyağcılar',
            'Pınarlı',
            'Pınaryaka',
            'Sarıcaova',
            'Sındı',
            'Taşkesiği',
            'Turgut',
            'Yaraş',
            'Yazır',
            'Yavuz Selim',
            'Yeniköy',
            'Yeşilbağcılar',
        ];

        $this->command->info("📍 Yatağan mahalleleri ekleniyor...");

        $eklenenSayisi = 0;
        $mevcutSayisi = 0;

        foreach ($mahalleler as $mahalleAdi) {
            $mahalle = Mahalle::firstOrCreate(
                [
                    'ilce_id' => $yatagan->id,
                    'mahalle_adi' => $mahalleAdi
                ],
                [
                    'mahalle_kodu' => null,
                    'posta_kodu' => null
                ]
            );

            if ($mahalle->wasRecentlyCreated) {
                $eklenenSayisi++;
                $this->command->line("  ✅ {$mahalleAdi}");
            } else {
                $mevcutSayisi++;
            }
        }

        $this->command->newLine();
        $this->command->info("📊 İşlem Özeti:");
        $this->command->table(
            ['Durum', 'Sayı'],
            [
                ['Yeni Eklenen', $eklenenSayisi],
                ['Zaten Mevcut', $mevcutSayisi],
                ['Toplam', count($mahalleler)]
            ]
        );

        $this->command->newLine();
        $this->command->info("🎉 Yatağan mahalleleri başarıyla yüklendi!");
        $this->command->line("   📍 İlçe: Yatağan (ID: {$yatagan->id})");
        $this->command->line("   🏘️  Mahalle: " . count($mahalleler) . " adet");
    }
}
