<?php

namespace Database\Seeders;

use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use Illuminate\Database\Seeder;

class MilasMahallelerSeeder extends Seeder
{
    /**
     * Milas mahalleleri - Standart format (Mahallesi kelimesi yok)
     */
    public function run(): void
    {
        // Muğla ilini bul
        $mugla = Il::where('il_adi', 'Muğla')->first();

        if (! $mugla) {
            $this->command->error('❌ Muğla ili bulunamadı!');

            return;
        }

        // Milas ilçesini bul veya oluştur
        $milas = Ilce::firstOrCreate(
            ['il_id' => $mugla->id, 'ilce_adi' => 'Milas']
        );

        $this->command->info("✅ Milas ilçesi: ID #{$milas->id}");

        // Milas Mahalleleri (Alfabetik sıralı)
        $mahalleler = [
            'Ahmetçelebi',
            'Akçaova',
            'Akgedik',
            'Akyeniköy',
            'Alatepe',
            'Altıntaş',
            'Anbarcık',
            'Ariburnu',
            'Avşar',
            'Bafa',
            'Bahçeyaka',
            'Balcılar',
            'Balıklıova',
            'Bozalan',
            'Bozarmut',
            'Bozyazı',
            'Çamköy',
            'Çamlık',
            'Çiftlik',
            'Çomaklı',
            'Danişment',
            'Deliilyas',
            'Demirciler',
            'Dereköy',
            'Ekindere',
            'Emre',
            'Gebekum',
            'Gökbel',
            'Güllük',
            'Güneyce',
            'Güvercinlik',
            'Hacıilyas',
            'Hasanköy',
            'Hüssamlar',
            'İkizce',
            'İnköy',
            'Kafaca',
            'Kapıkırı',
            'Karacahisar',
            'Karakuyu',
            'Kargıcak',
            'Kavaklı',
            'Kazıklı',
            'Kırcağız',
            'Kıyıkışlacık',
            'Koçarlı',
            'Konacık',
            'Koru',
            'Kuyucak',
            'Lalebahçe',
            'Lale',
            'Mersinçeşme',
            'Muğla',
            'Ören',
            'Pınarlı',
            'Savran',
            'Selimiye',
            'Sodra',
            'Şamata',
            'Turgut',
            'Uzunyuva',
            'Yalınayak',
            'Yaşyer',
            'Yeniköy',
            'Yerkesik',
            'Yeşilköy',
            'Yoran',
        ];

        $this->command->info('📍 Milas mahalleleri ekleniyor...');

        $eklenenSayisi = 0;
        $mevcutSayisi = 0;

        foreach ($mahalleler as $mahalleAdi) {
            $mahalle = Mahalle::firstOrCreate(
                [
                    'ilce_id' => $milas->id,
                    'mahalle_adi' => $mahalleAdi,
                ],
                [
                    'mahalle_kodu' => null,
                    'posta_kodu' => null,
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
        $this->command->info('🎉 Milas mahalleleri başarıyla yüklendi!');
        $this->command->line("   📍 İlçe: Milas (ID: {$milas->id})");
        $this->command->line('   🏘️  Mahalle: '.count($mahalleler).' adet');
    }
}
