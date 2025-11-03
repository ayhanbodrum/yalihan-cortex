<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;

class YayinTipleriMasterSeeder extends Seeder
{
    /**
     * 20 Yayın Tipi - Kapsamlı Tanımlamalar
     * Context7 Compliant - 2025-10-23
     * 
     * NOT: Yayın tipleri ilan_kategori_yayin_tipleri tablosunda
     * Her yayın tipi birden fazla kategoriye bağlanabilir
     */
    public function run(): void
    {
        // ✅ 20 Detaylı Yayın Tipi Tanımı
        $yayinTipleriTanim = [
            // ====== ANA YAYIN TİPLERİ (5) ======
            [
                'yayin_tipi' => 'Satılık',
                'aciklama' => 'Mülkiyeti devredilecek gayrimenkul. Tapu devri ile satış.',
                'kategori_uygunluk' => ['Tüm kategoriler'],
                'min_alan_m2' => 20,
                'min_fiyat' => 100000,
                'order' => 1,
            ],
            [
                'yayin_tipi' => 'Kiralık',
                'aciklama' => 'Uzun süreli kiralık (yıllık). Minimum 12 ay sözleşme.',
                'kategori_uygunluk' => ['Tüm kategoriler'],
                'min_alan_m2' => null,
                'min_fiyat' => 1000,
                'order' => 2,
            ],
            [
                'yayin_tipi' => 'Günlük Kiralık',
                'aciklama' => 'Kısa süreli kiralama (1-30 gün). Tatil veya iş gezisi için.',
                'kategori_uygunluk' => ['Yazlık', 'Apart', 'Villa', 'Turistik Tesisler'],
                'min_alan_m2' => null,
                'min_fiyat' => 500,
                'order' => 3,
            ],
            [
                'yayin_tipi' => 'Sezonluk Kiralık',
                'aciklama' => 'Sezonluk kiralama (3-6 ay). Yaz veya kış sezonu için.',
                'kategori_uygunluk' => ['Yazlık', 'Villa', 'Turistik Tesisler'],
                'min_alan_m2' => null,
                'min_fiyat' => 10000,
                'order' => 4,
            ],
            [
                'yayin_tipi' => 'Devren',
                'aciklama' => 'İşletme devri. Mal varlığı, ruhsat ve ciro ile birlikte devir.',
                'kategori_uygunluk' => ['Dükkan', 'Restaurant/Cafe', 'Otel', 'Fabrika'],
                'min_alan_m2' => null,
                'min_fiyat' => 50000,
                'order' => 5,
            ],

            // ====== ÖZEL YAYIN TİPLERİ (15) ======
            [
                'yayin_tipi' => 'Kat Karşılığı',
                'aciklama' => 'Arsa karşılığı inşaat anlaşması. İnşaatçıya arsa, karşılığında daire/kat.',
                'kategori_uygunluk' => ['İmarlı Arsa', 'Turistik Arsa', 'Ticari Arsa'],
                'min_alan_m2' => 200,
                'min_fiyat' => null,
                'order' => 6,
            ],
            [
                'yayin_tipi' => 'Yatırımlık',
                'aciklama' => 'Yatırım amaçlı satış. Yüksek getiri potansiyeli, değer artışı beklentisi.',
                'kategori_uygunluk' => ['Tüm kategoriler'],
                'min_alan_m2' => null,
                'min_fiyat' => null,
                'order' => 7,
            ],
            [
                'yayin_tipi' => 'Acil Satılık',
                'aciklama' => 'Hızlı satış için indirimli fiyat. Pazarlık yapılabilir, anında teslim.',
                'kategori_uygunluk' => ['Tüm kategoriler'],
                'min_alan_m2' => null,
                'min_fiyat' => null,
                'order' => 8,
            ],
            [
                'yayin_tipi' => 'İhaleli Satış',
                'aciklama' => 'İhale ile satılacak. İhale tarihi ve şartnamesi belirtilmelidir.',
                'kategori_uygunluk' => ['Arsa', 'İşyeri', 'Turistik Tesis'],
                'min_alan_m2' => null,
                'min_fiyat' => null,
                'order' => 9,
            ],
            [
                'yayin_tipi' => 'Trampalı',
                'aciklama' => 'Takas ile satış. Araç, başka emlak veya altın ile takas edilebilir.',
                'kategori_uygunluk' => ['Tüm kategoriler'],
                'min_alan_m2' => null,
                'min_fiyat' => null,
                'order' => 10,
            ],
            [
                'yayin_tipi' => 'Krediye Uygun',
                'aciklama' => 'Banka kredisi çıkacak. Tapu temiz, kredi için gerekli belgeler hazır.',
                'kategori_uygunluk' => ['Konut', 'İşyeri'],
                'min_alan_m2' => null,
                'min_fiyat' => null,
                'order' => 11,
            ],
            [
                'yayin_tipi' => 'Lüks Segment',
                'aciklama' => 'Lüks emlak kategorisi. Premium lokasyon, yüksek kalite malzeme ve özel tasarım.',
                'kategori_uygunluk' => ['Villa', 'Residence', 'Plaza/AVM'],
                'min_alan_m2' => 150,
                'min_fiyat' => 5000000,
                'order' => 12,
            ],
            [
                'yayin_tipi' => 'İnşaat Halinde',
                'aciklama' => 'Yapım aşamasında gayrimenkul. Teslim tarihi ve tamamlanma oranı belirtilmelidir.',
                'kategori_uygunluk' => ['Konut Projesi', 'Villa', 'Daire'],
                'min_alan_m2' => null,
                'min_fiyat' => null,
                'order' => 13,
            ],
            [
                'yayin_tipi' => 'Sıfır/Yeni',
                'aciklama' => 'Hiç kullanılmamış veya 0-2 yaşında gayrimenkul. İlk sahibinden.',
                'kategori_uygunluk' => ['Konut', 'İşyeri'],
                'min_alan_m2' => null,
                'min_fiyat' => null,
                'order' => 14,
            ],
            [
                'yayin_tipi' => 'Öğrenci Evi',
                'aciklama' => 'Öğrencilere özel kiralık. Üniversite yakını, eşyalı veya eşyasız.',
                'kategori_uygunluk' => ['Daire', 'Apart', 'Müstakil Ev'],
                'min_alan_m2' => 40,
                'min_fiyat' => null,
                'order' => 15,
            ],
            [
                'yayin_tipi' => 'Sahibinden',
                'aciklama' => 'Aracısız satış/kiralık. Komisyon yok, doğrudan mal sahibi ile görüşme.',
                'kategori_uygunluk' => ['Tüm kategoriler'],
                'min_alan_m2' => null,
                'min_fiyat' => null,
                'order' => 16,
            ],
            [
                'yayin_tipi' => 'Ofis Dönüşümlü',
                'aciklama' => 'Ofisten konuta veya konuttan ofise dönüştürülebilir. Ruhsat gereklidir.',
                'kategori_uygunluk' => ['Daire', 'Ofis'],
                'min_alan_m2' => 50,
                'min_fiyat' => null,
                'order' => 17,
            ],
            [
                'yayin_tipi' => 'Sosyal Konut',
                'aciklama' => 'Devlet destekli konut projesi. TOKİ, kampanya veya indirimli satış.',
                'kategori_uygunluk' => ['Konut Projesi', 'Daire'],
                'min_alan_m2' => null,
                'min_fiyat' => null,
                'order' => 18,
            ],
            [
                'yayin_tipi' => 'Ön Satış',
                'aciklama' => 'Proje aşamasında satış. Gelecek teslim tarihi, indirimli fiyat.',
                'kategori_uygunluk' => ['Projeler'],
                'min_alan_m2' => null,
                'min_fiyat' => null,
                'order' => 19,
            ],
            [
                'yayin_tipi' => 'Resmi Kurum',
                'aciklama' => 'Resmi kurum (belediye, hazine) tarafından satılan emlak.',
                'kategori_uygunluk' => ['Tüm kategoriler'],
                'min_alan_m2' => null,
                'min_fiyat' => null,
                'order' => 20,
            ],
        ];

        // ✅ Her Kategoriye Uygun Yayın Tiplerini Eşleştir
        $kategoriYayinEslesmesi = [
            // ARSA Kategorileri
            'imarli-arsa' => ['Satılık', 'Kiralık', 'Kat Karşılığı', 'Yatırımlık', 'Acil Satılık', 'İhaleli Satış', 'Trampalı'],
            'tarla' => ['Satılık', 'Kiralık', 'Yatırımlık', 'İhaleli Satış', 'Trampalı'],
            'zeytinlik' => ['Satılık', 'Kiralık', 'Yatırımlık', 'Trampalı'],
            'bag' => ['Satılık', 'Kiralık', 'Yatırımlık', 'Trampalı'],
            'bahce' => ['Satılık', 'Kiralık', 'Yatırımlık'],
            'ciftlik' => ['Satılık', 'Kiralık', 'Yatırımlık', 'Trampalı'],
            'turistik-arsa' => ['Satılık', 'Kiralık', 'Kat Karşılığı', 'Yatırımlık', 'İhaleli Satış'],
            'sanayi-arsasi' => ['Satılık', 'Kiralık', 'Yatırımlık', 'İhaleli Satış'],
            'ticari-arsa' => ['Satılık', 'Kiralık', 'Kat Karşılığı', 'Yatırımlık', 'İhaleli Satış'],
            'karma-alan' => ['Satılık', 'Kat Karşılığı', 'Yatırımlık'],
            'mesire-alani' => ['Satılık', 'Kiralık', 'Yatırımlık'],

            // KONUT Kategorileri
            'villa' => ['Satılık', 'Kiralık', 'Günlük Kiralık', 'Sezonluk Kiralık', 'Yatırımlık', 'Lüks Segment', 'Sıfır/Yeni', 'Krediye Uygun'],
            'daire' => ['Satılık', 'Kiralık', 'Yatırımlık', 'Acil Satılık', 'Krediye Uygun', 'Sıfır/Yeni', 'Öğrenci Evi', 'Sahibinden', 'Ofis Dönüşümlü'],
            'yazlik' => ['Satılık', 'Kiralık', 'Günlük Kiralık', 'Sezonluk Kiralık', 'Yatırımlık'],
            'residence' => ['Satılık', 'Kiralık', 'Lüks Segment', 'Yatırımlık', 'Krediye Uygun', 'Sıfır/Yeni'],
            'mustakil-ev' => ['Satılık', 'Kiralık', 'Yatırımlık', 'Krediye Uygun', 'Öğrenci Evi'],
            'apart' => ['Kiralık', 'Günlük Kiralık', 'Öğrenci Evi', 'Sahibinden'],

            // İŞYERİ Kategorileri
            'dukkan' => ['Satılık', 'Kiralık', 'Devren', 'Yatırımlık', 'Sahibinden'],
            'ofis' => ['Satılık', 'Kiralık', 'Yatırımlık', 'Krediye Uygun', 'Ofis Dönüşümlü'],
            'restaurant-cafe' => ['Kiralık', 'Devren', 'Yatırımlık'],
            'fabrika' => ['Satılık', 'Kiralık', 'Devren', 'İhaleli Satış'],
            'plaza-avm' => ['Satılık', 'Kiralık', 'Lüks Segment', 'Yatırımlık'],

            // TURİSTİK TESİS Kategorileri
            'otel' => ['Satılık', 'Kiralık', 'Devren', 'Yatırımlık'],
            'pansiyon' => ['Satılık', 'Kiralık', 'Devren'],
            'apart-otel' => ['Satılık', 'Kiralık', 'Devren', 'Yatırımlık'],
            'butik-otel' => ['Satılık', 'Kiralık', 'Devren', 'Lüks Segment'],

            // PROJE Kategorileri
            'konut-projesi' => ['Satılık', 'Ön Satış', 'İnşaat Halinde', 'Sosyal Konut', 'Krediye Uygun'],
            'villa-projesi' => ['Satılık', 'Ön Satış', 'İnşaat Halinde', 'Lüks Segment'],
            'residence-projesi' => ['Satılık', 'Ön Satış', 'İnşaat Halinde', 'Lüks Segment', 'Yatırımlık'],
            'ticari-proje' => ['Satılık', 'Ön Satış', 'İnşaat Halinde', 'Yatırımlık'],
        ];

        $totalCreated = 0;

        foreach ($kategoriYayinEslesmesi as $kategoriSlug => $yayinTipleriList) {
            $kategori = IlanKategori::where('slug', $kategoriSlug)->first();

            if (!$kategori) {
                $this->command->warn("⚠️  Kategori bulunamadı: {$kategoriSlug}");
                continue;
            }

            foreach ($yayinTipleriList as $yayinTipiAdi) {
                // Yayın tipi tanımını bul
                $yayinTipiTanim = collect($yayinTipleriTanim)->firstWhere('yayin_tipi', $yayinTipiAdi);

                // Eğer tanım yoksa basit ekle
                if (!$yayinTipiTanim) {
                    $yayinTipiTanim = [
                        'yayin_tipi' => $yayinTipiAdi,
                        'aciklama' => null,
                        'min_alan_m2' => null,
                        'min_fiyat' => null,
                        'order' => 99,
                    ];
                }

                $created = IlanKategoriYayinTipi::updateOrCreate(
                    [
                        'kategori_id' => $kategori->id,
                        'yayin_tipi' => $yayinTipiAdi,
                    ],
                    [
                        'status' => 'Aktif',
                        'order' => $yayinTipiTanim['order'] ?? 99,
                    ]
                );

                if ($created->wasRecentlyCreated) {
                    $totalCreated++;
                }
            }

            $this->command->info("✅ {$kategori->name}: " . count($yayinTipleriList) . " yayın tipi");
        }

        $this->command->info("\n📊 YAYIN TİPİ İSTATİSTİKLERİ:");
        $this->command->info("   ✅ Yeni eklenen: {$totalCreated}");
        $this->command->info("   📦 Toplam: " . IlanKategoriYayinTipi::count());
        $this->command->info("   🎯 Benzersiz tip: " . IlanKategoriYayinTipi::distinct('yayin_tipi')->count('yayin_tipi'));
    }
}

