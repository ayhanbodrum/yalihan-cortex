<?php

namespace Database\Seeders;

use App\Models\Il;
use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;
use App\Models\Ilce;
use App\Models\Mahalle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PortfolioDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Portfolio demo verileri yükleniyor...');

        // Transaction içinde çalıştır - daha hızlı ve güvenli
        DB::transaction(function () {
            // Kategorileri ve yayın tiplerini önceden yükle
            $yayinTipiSatilik = IlanKategoriYayinTipi::where('yayin_tipi', 'Satılık')->first();
            $yayinTipiKiralik = IlanKategoriYayinTipi::where('yayin_tipi', 'Kiralık')->first();
            $yayinTipiYazlik = IlanKategoriYayinTipi::where('yayin_tipi', 'Yazlık Kiralık')->first();

            $kategoriArsa = IlanKategori::where('slug', 'arsa')->first()
                ?? IlanKategori::whereNull('parent_id')->where('name', 'Arsa')->first();
            $kategoriKonut = IlanKategori::where('slug', 'konut')->first()
                ?? IlanKategori::whereNull('parent_id')->where('name', 'Konut')->first();
            $kategoriYazlik = IlanKategori::where('slug', 'yazlik-kiralik')->first()
                ?? IlanKategori::whereNull('parent_id')->where('name', 'Yazlık Kiralık')->first();

            $altKonutVilla = $kategoriKonut?->children()->where('slug', 'villa')->first();
            $altKonutDaire = $kategoriKonut?->children()->where('slug', 'daire')->first();
            $altYazlikVilla = $kategoriYazlik?->children()->where('slug', 'mustakil')->first();
            $altArsa = $kategoriArsa?->children()->where('slug', 'konut-imarli')->first()
                ?? $kategoriArsa?->children()->first();

            // Tüm kayıtları hazırla
            $arseRecords = $this->seedArsaPortfolio($kategoriArsa, $altArsa, $yayinTipiSatilik);
            $konutRecords = $this->seedKonutPortfolio($kategoriKonut, $altKonutVilla, $altKonutDaire, $yayinTipiSatilik);
            $yazlikRecords = $this->seedYazlikPortfolio($kategoriYazlik, $altYazlikVilla, $yayinTipiYazlik);

            $allRecords = collect([$arseRecords, $konutRecords, $yazlikRecords])->flatten(1);

            // Location verilerini önceden hazırla ve cache'le
            $il = Il::firstOrCreate(['il_adi' => 'Muğla'], ['plaka_kodu' => '48']);
            $ilce = Ilce::firstOrCreate(['il_id' => $il->id, 'ilce_adi' => 'Bodrum']);

            // Mahalleleri toplu olarak hazırla
            $mahalleler = [];
            $allRecords->each(function ($payload) use ($ilce, &$mahalleler) {
                $mahalleAdi = $payload['location']['mahalle'];
                if (! isset($mahalleler[$mahalleAdi])) {
                    $mahalleler[$mahalleAdi] = Mahalle::firstOrCreate(
                        ['ilce_id' => $ilce->id, 'mahalle_adi' => $mahalleAdi]
                    );
                }
            });

            // İlanları toplu olarak oluştur
            $allRecords->each(function ($payload) use ($il, $ilce, $mahalleler) {
                $mahalle = $mahalleler[$payload['location']['mahalle']];
                $data = $payload['data'];
                $data['il_id'] = $il->id;
                $data['ilce_id'] = $ilce->id;
                $data['mahalle_id'] = $mahalle->id;
                $data['status'] = 'yayinda'; // Context7: enum değeri

                // Database'de olmayan field'ları kaldır
                $data = array_diff_key($data, ['ilan_turu' => true]);

                // Model cast'ini atlamak için DB::table kullan
                $existing = DB::table('ilanlar')
                    ->where('baslik', $data['baslik'])
                    ->where('il_id', $il->id)
                    ->where('mahalle_id', $mahalle->id)
                    ->first();

                $now = now();
                if ($existing) {
                    $data['updated_at'] = $now;
                    DB::table('ilanlar')
                        ->where('id', $existing->id)
                        ->update($data);
                } else {
                    $data['created_at'] = $now;
                    $data['updated_at'] = $now;
                    DB::table('ilanlar')->insert($data);
                }
            });
        });

        $this->command->info('✅ Portfolio demo verileri başarıyla yüklendi!');
    }

    private function seedArsaPortfolio(?IlanKategori $ana, ?IlanKategori $alt, ?IlanKategoriYayinTipi $yayinTipi): array
    {
        $records = [
            ['title' => 'Yalıkavak Geriş\'te %50 Konut İmarlı Manzaralı Arsa', 'price' => 14775000, 'area' => 350, 'neighborhood' => 'Geriş Mh.', 'features' => ['Deniz,Doğa,Gün Batımı', 'Elektrik,Su,Telefon,Sanayi Elektriği', 'Ana Yola Yakın,Denize Yakın,Toplu Ulaşıma Yakın', 'Parselli,Köşe Parsel,İfrazlı']],
            ['title' => 'Gündoğan Kızılburun\'da Zeytinlik içinde Tarla', 'price' => 8750000, 'area' => 420, 'neighborhood' => 'Gündoğan Mh.', 'features' => ['Deniz,Doğa', 'Elektrik,Su,Telefon', 'Denize Yakın,Toplu Ulaşıma Yakın', 'Köşe Parsel']],
            ['title' => 'Turgutreis Kadıkalesi\'nde Projeli Arsalar', 'price' => 11250000, 'area' => 500, 'neighborhood' => 'Turgutreis Mh.', 'features' => ['Deniz,Doğa,Şehir', 'Elektrik,Su,Kanalizasyon', 'Ana Yola Yakın,Havaalanına Yakın', 'Projeli,Parselli']],
            ['title' => 'Bitez\'de İmar Planına Yakın Yatırım Arsası', 'price' => 9650000, 'area' => 380, 'neighborhood' => 'Bitez Mh.', 'features' => ['Doğa,Şehir', 'Elektrik,Su,Telefon', 'Toplu Ulaşıma Yakın', 'Parselli']],
            ['title' => 'Gümüşlük Koyunbaba\'da Gün Batımı Manzaralı Arsa', 'price' => 12350000, 'area' => 460, 'neighborhood' => 'Gümüşlük Mh.', 'features' => ['Deniz,Doğa', 'Elektrik,Su', 'Denize Yakın,Ana Yola Yakın', 'İfrazlı']],
        ];

        return collect($records)->map(function ($record) use ($ana, $alt, $yayinTipi) {
            return [
                'location' => [
                    'il' => 'Muğla',
                    'ilce' => 'Bodrum',
                    'mahalle' => $record['neighborhood'],
                ],
                'data' => [
                    'baslik' => $record['title'],
                    'slug' => Str::slug($record['title']).'-'.Str::random(5),
                    'aciklama' => "<p>{$record['title']}</p>",
                    'fiyat' => $record['price'],
                    'para_birimi' => 'TRY',
                    'brut_m2' => $record['area'],
                    'net_m2' => $record['area'],
                    'status' => 'yayinda', // Context7: enum değeri
                    'yayinlama_tipi' => 'Satılık',
                    'imar_durumu' => 'Konut',
                    'manzara' => $record['features'][0],
                    'altyapi' => $record['features'][1],
                    'konum_ozellikleri' => $record['features'][2],
                    'genel_ozellikler' => $record['features'][3],
                    'ana_kategori_id' => $ana?->id,
                    'alt_kategori_id' => $alt?->id,
                    'yayin_tipi_id' => $yayinTipi?->id,
                ],
            ];
        })->all();
    }

    private function seedKonutPortfolio(?IlanKategori $ana, ?IlanKategori $villa, ?IlanKategori $daire, ?IlanKategoriYayinTipi $yayinTipi): array
    {
        $records = [
            [
                'title' => 'Yalıkavak Tilkicik Koyu\'nda Panoramik Deniz Manzaralı Villa',
                'price' => 32500000,
                'brut' => 220,
                'net' => 185,
                'beds' => 4,
                'baths' => 3,
                'type' => 'villa',
                'neighborhood' => 'Yalıkavak Mh.',
                'features' => 'Özel Havuz,Akıllı Ev Sistemi,Kapalı Otopark,İskanlı',
                'view' => 'Geniş Deniz Manzarası',
            ],
            [
                'title' => 'Türkbükü Sahil Çizgisine 100 m Mesafede Modern Villa',
                'price' => 45000000,
                'brut' => 240,
                'net' => 210,
                'beds' => 5,
                'baths' => 4,
                'type' => 'villa',
                'neighborhood' => 'Göltürkbükü Mh.',
                'features' => 'Isıtmalı Havuz,Özel İskelesi,Sauna',
                'view' => 'Deniz & Adalar',
            ],
            [
                'title' => 'Bodrum Marina Bölgesinde Loft Tarzı Deniz Manzaralı Daire',
                'price' => 12750000,
                'brut' => 140,
                'net' => 110,
                'beds' => 2,
                'baths' => 2,
                'type' => 'daire',
                'neighborhood' => 'Eskiçeşme Mh.',
                'features' => 'Site Güvenliği,Açık Otopark,Merkezi Klima',
                'view' => 'Marina ve Kale',
            ],
            [
                'title' => 'Bitez Sarnıç Sokak\'ta Bahçe Kullanımlı Dubleks Daire',
                'price' => 18900000,
                'brut' => 180,
                'net' => 150,
                'beds' => 3,
                'baths' => 3,
                'type' => 'daire',
                'neighborhood' => 'Bitez Mh.',
                'features' => 'Geniş Bahçe,Teras Alanı,Kapalı Garaj',
                'view' => 'Bahçe',
            ],
            [
                'title' => 'Ortakent Yahşi\'de Özel Plaja Yakın 5+2 Lüks Villa',
                'price' => 52000000,
                'brut' => 320,
                'net' => 260,
                'beds' => 5,
                'baths' => 5,
                'type' => 'villa',
                'neighborhood' => 'Ortakentyahşi Mh.',
                'features' => 'Sonsuzluk Havuzu,Asansör,Çift Kitchen',
                'view' => 'Deniz & Doğa',
            ],
        ];

        return collect($records)->map(function ($record) use ($ana, $villa, $daire, $yayinTipi) {
            $altKategoriId = $record['type'] === 'villa' ? $villa?->id : $daire?->id;

            return [
                'location' => [
                    'il' => 'Muğla',
                    'ilce' => 'Bodrum',
                    'mahalle' => $record['neighborhood'],
                ],
                'data' => [
                    'baslik' => $record['title'],
                    'slug' => Str::slug($record['title']).'-'.Str::random(5),
                    'aciklama' => "<p>{$record['title']}</p>",
                    'fiyat' => $record['price'],
                    'para_birimi' => 'TRY',
                    'brut_m2' => $record['brut'],
                    'net_m2' => $record['net'],
                    'oda_sayisi' => $record['beds'],
                    'banyo_sayisi' => $record['baths'],
                    'status' => 'yayinda', // Context7: enum değeri
                    'yayinlama_tipi' => 'Satılık',
                    'konut_tipi' => $record['type'],
                    'manzara' => $record['view'],
                    'konut_ozellikleri' => $record['features'],
                    'ana_kategori_id' => $ana?->id,
                    'alt_kategori_id' => $altKategoriId,
                    'yayin_tipi_id' => $yayinTipi?->id,
                ],
            ];
        })->all();
    }

    private function seedYazlikPortfolio(?IlanKategori $ana, ?IlanKategori $alt, ?IlanKategoriYayinTipi $yayinTipi): array
    {
        $records = [
            [
                'title' => 'Gündoğan Sahilde Gün Batımlı Infinity Pool Villa',
                'price' => 185000,
                'brut' => 230,
                'net' => 200,
                'beds' => 4,
                'baths' => 3,
                'neighborhood' => 'Gündoğan Mh.',
                'features' => 'Özel Havuz,Dek Panoramik Teras,Doğrudan Sahile Servis',
                'view' => 'Deniz Panoraması',
            ],
            [
                'title' => 'Yalıkavak Tilkicik Koyu\'nda Wooden Concept Haftalık Kiralık Villa',
                'price' => 165000,
                'brut' => 210,
                'net' => 180,
                'beds' => 3,
                'baths' => 3,
                'neighborhood' => 'Yalıkavak Mh.',
                'features' => 'Isıtmalı Havuz,Akıllı Ev,Butler Servisi',
                'view' => 'Deniz & Adalar',
            ],
            [
                'title' => 'Bitez Beach Loft 2+1 Yazlık Daire',
                'price' => 95000,
                'brut' => 120,
                'net' => 95,
                'beds' => 2,
                'baths' => 2,
                'neighborhood' => 'Bitez Mh.',
                'features' => 'Site Havuzu,Kapalı Otopark,Beach Club Üyeliği',
                'view' => 'Bahçe & Deniz',
            ],
            [
                'title' => 'Göktürkbükü Boho Tarzı 5+1 Özel Vadi Villas',
                'price' => 210000,
                'brut' => 260,
                'net' => 220,
                'beds' => 5,
                'baths' => 4,
                'neighborhood' => 'Göltürkbükü Mh.',
                'features' => 'Geniş Bahçe,Yoga Deck,Özel Şef Hizmeti',
                'view' => 'Deniz & Doğa',
            ],
            [
                'title' => 'Torba Family House - Özel Isıtmalı Havuzlu Yazlık',
                'price' => 120000,
                'brut' => 200,
                'net' => 170,
                'beds' => 4,
                'baths' => 3,
                'neighborhood' => 'Torba Mh.',
                'features' => 'Isıtmalı Havuz,Çocuk Oyun Alanı,Şömine',
                'view' => 'Doğa',
            ],
        ];

        return collect($records)->map(function ($record) use ($ana, $alt, $yayinTipi) {
            return [
                'location' => [
                    'il' => 'Muğla',
                    'ilce' => 'Bodrum',
                    'mahalle' => $record['neighborhood'],
                ],
                'data' => [
                    'baslik' => $record['title'],
                    'slug' => Str::slug($record['title']).'-'.Str::random(5),
                    'aciklama' => "<p>{$record['title']}</p>",
                    'fiyat' => $record['price'],
                    'para_birimi' => 'TRY',
                    'brut_m2' => $record['brut'],
                    'net_m2' => $record['net'],
                    'oda_sayisi' => $record['beds'],
                    'banyo_sayisi' => $record['baths'],
                    'status' => 'yayinda', // Context7: enum değeri
                    'yayinlama_tipi' => 'Yazlık Kiralık',
                    'kiralama_tipi' => 'Haftalık',
                    'konut_tipi' => 'yazlik',
                    'manzara' => $record['view'],
                    'konut_ozellikleri' => $record['features'],
                    'ana_kategori_id' => $ana?->id,
                    'alt_kategori_id' => $alt?->id,
                    'yayin_tipi_id' => $yayinTipi?->id,
                ],
            ];
        })->all();
    }
}
