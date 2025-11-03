<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Ulke;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Önce tabloları temizle
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Mahalle::truncate();
        Ilce::truncate();
        Il::truncate();
        Ulke::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Türkiye'yi ekle
        $turkiye = Ulke::create([
            'ulke_adi' => 'Türkiye',
            'ulke_kodu' => 'TR',
            'telefon_kodu' => '+90'
        ]);

        // Muğla ili ve ilçeleri (Bodrum merkez)
        $mugla = Il::create([
            'il_adi' => 'Muğla',
            'plaka_kodu' => '48',
            'ulke_id' => $turkiye->id
        ]);

        // Bodrum ve çevresindeki ilçeler
        $bodrum = Ilce::create([
            'ilce_adi' => 'Bodrum',
            'il_id' => $mugla->id,
            'ilce_kodu' => 'BDR'
        ]);

        $marmaris = Ilce::create([
            'ilce_adi' => 'Marmaris',
            'il_id' => $mugla->id,
            'ilce_kodu' => 'MRM'
        ]);

        $datca = Ilce::create([
            'ilce_adi' => 'Datça',
            'il_id' => $mugla->id,
            'ilce_kodu' => 'DAT'
        ]);

        $koycegiz = Ilce::create([
            'ilce_adi' => 'Köyceğiz',
            'il_id' => $mugla->id,
            'ilce_kodu' => 'KYG'
        ]);

        $ortaca = Ilce::create([
            'ilce_adi' => 'Ortaca',
            'il_id' => $mugla->id,
            'ilce_kodu' => 'ORT'
        ]);

        $dalaman = Ilce::create([
            'ilce_adi' => 'Dalaman',
            'il_id' => $mugla->id,
            'ilce_kodu' => 'DAL'
        ]);

        $fethiye = Ilce::create([
            'ilce_adi' => 'Fethiye',
            'il_id' => $mugla->id,
            'ilce_kodu' => 'FET'
        ]);

        $mentese = Ilce::create([
            'ilce_adi' => 'Menteşe',
            'il_id' => $mugla->id,
            'ilce_kodu' => 'MEN'
        ]);

        $yatalan = Ilce::create([
            'ilce_adi' => 'Yatağan',
            'il_id' => $mugla->id,
            'ilce_kodu' => 'YAT'
        ]);

        $uła = Ilce::create([
            'ilce_adi' => 'Ula',
            'il_id' => $mugla->id,
            'ilce_kodu' => 'ULA'
        ]);

        $kavaklidere = Ilce::create([
            'ilce_adi' => 'Kavaklıdere',
            'il_id' => $mugla->id,
            'ilce_kodu' => 'KAV'
        ]);

        // Bodrum mahalleleri (popüler yerler)
        $bodrumMahalleleri = [
            'Bodrum Merkez',
            'Gümbet',
            'Bitez',
            'Ortakent',
            'Yalıkavak',
            'Türkbükü',
            'Gölköy',
            'Torba',
            'Gündoğan',
            'Kadıkalesi',
            'Çamlık',
            'Konacık',
            'Çiftlik',
            'Mumcular',
            'Karaova',
            'Mazıköy',
            'Kızılağaç',
            'Akçaalan',
            'Çömlekçi',
            'Karaada',
            'Bardakçı',
            'Aspat',
            'Etrim',
            'Kızılağaç',
            'Peksimet',
            'Pınarlıbelen',
            'Sazköy',
            'Tepecik',
            'Yalıçiftlik'
        ];

        foreach ($bodrumMahalleleri as $mahalle) {
            Mahalle::create([
                'mahalle_adi' => $mahalle,
                'ilce_id' => $bodrum->id,
                'enlem' => 37.0346 + (rand(-100, 100) / 10000), // Bodrum merkez etrafında
                'boylam' => 27.4309 + (rand(-100, 100) / 10000)
            ]);
        }

        // Marmaris mahalleleri
        $marmarisMahalleleri = [
            'Marmaris Merkez',
            'İçmeler',
            'Armutalan',
            'Beldibi',
            'Siteler',
            'Tepe',
            'Adaköy',
            'Bayır',
            'Bozburun',
            'Çamlıyurt',
            'Hisarönü',
            'Kumlubük',
            'Orhaniye',
            'Selimiye',
            'Turunç'
        ];

        foreach ($marmarisMahalleleri as $mahalle) {
            Mahalle::create([
                'mahalle_adi' => $mahalle,
                'ilce_id' => $marmaris->id,
                'enlem' => 36.8549 + (rand(-50, 50) / 10000),
                'boylam' => 28.2708 + (rand(-50, 50) / 10000)
            ]);
        }

        // Datça mahalleleri
        $datcaMahalleleri = [
            'Datça Merkez',
            'Eski Datça',
            'Kızlan',
            'Mesudiye',
            'Reşadiye',
            'Palamutbükü',
            'Knidos',
            'Emecik',
            'Karaköy',
            'Sındı',
            'Yazıköy',
            'Cumalı',
            'Hızırşah',
            'İskele',
            'Kızılbük'
        ];

        foreach ($datcaMahalleleri as $mahalle) {
            Mahalle::create([
                'mahalle_adi' => $mahalle,
                'ilce_id' => $datca->id,
                'enlem' => 36.7289 + (rand(-30, 30) / 10000),
                'boylam' => 27.6889 + (rand(-30, 30) / 10000)
            ]);
        }

        // Diğer önemli iller (İstanbul, Ankara, İzmir, Antalya)
        $onemliIller = [
            ['il_adi' => 'İstanbul', 'plaka_kodu' => '34'],
            ['il_adi' => 'Ankara', 'plaka_kodu' => '06'],
            ['il_adi' => 'İzmir', 'plaka_kodu' => '35'],
            ['il_adi' => 'Antalya', 'plaka_kodu' => '07'],
            ['il_adi' => 'Aydın', 'plaka_kodu' => '09'],
            ['il_adi' => 'Denizli', 'plaka_kodu' => '20'],
            ['il_adi' => 'Balıkesir', 'plaka_kodu' => '10'],
            ['il_adi' => 'Çanakkale', 'plaka_kodu' => '17']
        ];

        foreach ($onemliIller as $il) {
            $yeniIl = Il::create([
                'il_adi' => $il['il_adi'],
                'plaka_kodu' => $il['plaka_kodu'],
                'ulke_id' => $turkiye->id
            ]);

            // Her ile 3-5 ilçe ekle
            $ilceSayisi = rand(3, 5);
            for ($i = 1; $i <= $ilceSayisi; $i++) {
                $ilce = Ilce::create([
                    'ilce_adi' => $il['il_adi'] . ' İlçe ' . $i,
                    'il_id' => $yeniIl->id,
                    'ilce_kodu' => strtoupper(substr($il['il_adi'], 0, 3)) . $i
                ]);

                // Her ilçeye 2-3 mahalle ekle
                $mahalleSayisi = rand(2, 3);
                for ($j = 1; $j <= $mahalleSayisi; $j++) {
                    Mahalle::create([
                        'mahalle_adi' => $ilce->ilce_adi . ' Mahalle ' . $j,
                        'ilce_id' => $ilce->id,
                        'enlem' => 40.0000 + (rand(-500, 500) / 10000),
                        'boylam' => 30.0000 + (rand(-500, 500) / 10000)
                    ]);
                }
            }
        }

        $this->command->info('Lokasyon verileri başarıyla eklendi!');
        $this->command->info('Ülkeler: ' . Ulke::count());
        $this->command->info('İller: ' . Il::count());
        $this->command->info('İlçeler: ' . Ilce::count());
        $this->command->info('Mahalleler: ' . Mahalle::count());
    }
}
