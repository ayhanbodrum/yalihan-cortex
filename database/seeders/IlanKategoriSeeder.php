<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IlanKategori;
use Illuminate\Support\Str;

class IlanKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ana Kategoriler (Seviye 0)
        $anaKategoriler = [
            [
                'name' => 'Konut',
                'slug' => 'konut',
                'seviye' => 0,
                'status' => true,
                'display_order' => 1,
                'icon' => 'home',
                'description' => 'Daire, villa, müstakil ev gibi konut türleri'
            ],
            [
                'name' => 'İşyeri',
                'slug' => 'isyeri',
                'seviye' => 0,
                'status' => true,
                'display_order' => 2,
                'icon' => 'building',
                'description' => 'Ofis, dükkan, fabrika gibi ticari alanlar'
            ],
            [
                'name' => 'Arsa',
                'slug' => 'arsa',
                'seviye' => 0,
                'status' => true,
                'display_order' => 3,
                'icon' => 'map',
                'description' => 'İmar, tarım, orman arazileri'
            ],
            [
                'name' => 'Yazlık Kiralama',
                'slug' => 'yazlik-kiralama',
                'seviye' => 0,
                'status' => true,
                'display_order' => 4,
                'icon' => 'sun',
                'description' => 'Günlük, haftalık, aylık yazlık kiralama'
            ],
            [
                'name' => 'Turistik Tesisler',
                'slug' => 'turistik-tesisler',
                'seviye' => 0,
                'status' => true,
                'display_order' => 5,
                'icon' => 'hotel',
                'description' => 'Otel, pansiyon, tatil köyü gibi tesisler'
            ]
        ];

        $anaKategoriIds = [];
        foreach ($anaKategoriler as $kategori) {
            $anaKategori = IlanKategori::create($kategori);
            $anaKategoriIds[$kategori['slug']] = $anaKategori->id;
        }

        // Alt Kategoriler (Seviye 1)
        $altKategoriler = [
            // Konut Alt Kategorileri
            [
                'name' => 'Daire',
                'slug' => 'daire',
                'parent_id' => $anaKategoriIds['konut'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 1,
                'icon' => 'apartment',
                'description' => 'Apartman dairesi'
            ],
            [
                'name' => 'Villa',
                'slug' => 'villa',
                'parent_id' => $anaKategoriIds['konut'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 2,
                'icon' => 'villa',
                'description' => 'Müstakil villa'
            ],
            [
                'name' => 'Müstakil Ev',
                'slug' => 'mustakil-ev',
                'parent_id' => $anaKategoriIds['konut'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 3,
                'icon' => 'house',
                'description' => 'Tek ailelik müstakil ev'
            ],
            [
                'name' => 'Dubleks',
                'slug' => 'dubleks',
                'parent_id' => $anaKategoriIds['konut'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 4,
                'icon' => 'duplex',
                'description' => 'İki katlı konut'
            ],

            // İşyeri Alt Kategorileri
            [
                'name' => 'Ofis',
                'slug' => 'ofis',
                'parent_id' => $anaKategoriIds['isyeri'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 1,
                'icon' => 'office',
                'description' => 'Büro ve ofis alanları'
            ],
            [
                'name' => 'Dükkan',
                'slug' => 'dukkan',
                'parent_id' => $anaKategoriIds['isyeri'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 2,
                'icon' => 'shop',
                'description' => 'Perakende satış dükkanları'
            ],
            [
                'name' => 'Fabrika',
                'slug' => 'fabrika',
                'parent_id' => $anaKategoriIds['isyeri'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 3,
                'icon' => 'factory',
                'description' => 'Üretim tesisleri'
            ],
            [
                'name' => 'Depo',
                'slug' => 'depo',
                'parent_id' => $anaKategoriIds['isyeri'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 4,
                'icon' => 'warehouse',
                'description' => 'Depolama alanları'
            ],

            // Arsa Alt Kategorileri
            [
                'name' => 'İmar Arsaları',
                'slug' => 'imar-arsalari',
                'parent_id' => $anaKategoriIds['arsa'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 1,
                'icon' => 'land',
                'description' => 'İmar planına uygun arsalar'
            ],
            [
                'name' => 'Tarım Arazileri',
                'slug' => 'tarim-arazileri',
                'parent_id' => $anaKategoriIds['arsa'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 2,
                'icon' => 'farm',
                'description' => 'Tarımsal faaliyet için araziler'
            ],
            [
                'name' => 'Orman Arazileri',
                'slug' => 'orman-arazileri',
                'parent_id' => $anaKategoriIds['arsa'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 3,
                'icon' => 'forest',
                'description' => 'Ormanlık alanlar'
            ],

            // Yazlık Kiralama Alt Kategorileri
            [
                'name' => 'Günlük Kiralama',
                'slug' => 'gunluk-kiralama',
                'parent_id' => $anaKategoriIds['yazlik-kiralama'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 1,
                'icon' => 'calendar-day',
                'description' => 'Günlük yazlık kiralama'
            ],
            [
                'name' => 'Haftalık Kiralama',
                'slug' => 'haftalik-kiralama',
                'parent_id' => $anaKategoriIds['yazlik-kiralama'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 2,
                'icon' => 'calendar-week',
                'description' => 'Haftalık yazlık kiralama'
            ],
            [
                'name' => 'Aylık Kiralama',
                'slug' => 'aylik-kiralama',
                'parent_id' => $anaKategoriIds['yazlik-kiralama'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 3,
                'icon' => 'calendar-month',
                'description' => 'Aylık yazlık kiralama'
            ],

            // Turistik Tesisler Alt Kategorileri
            [
                'name' => 'Otel',
                'slug' => 'otel',
                'parent_id' => $anaKategoriIds['turistik-tesisler'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 1,
                'icon' => 'hotel',
                'description' => 'Otel tesisleri'
            ],
            [
                'name' => 'Pansiyon',
                'slug' => 'pansiyon',
                'parent_id' => $anaKategoriIds['turistik-tesisler'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 2,
                'icon' => 'pension',
                'description' => 'Pansiyon tesisleri'
            ],
            [
                'name' => 'Tatil Köyü',
                'slug' => 'tatil-koyu',
                'parent_id' => $anaKategoriIds['turistik-tesisler'],
                'seviye' => 1,
                'status' => true,
                'display_order' => 3,
                'icon' => 'resort',
                'description' => 'Tatil köyü tesisleri'
            ]
        ];

        $altKategoriIds = [];
        foreach ($altKategoriler as $kategori) {
            $altKategori = IlanKategori::create($kategori);
            $altKategoriIds[$kategori['slug']] = $altKategori->id;
        }

        // Yayın Tipleri (Seviye 2)
        $yayinTipleri = [
            // Daire Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['daire'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık daire'
            ],
            [
                'name' => 'Kiralık',
                'slug' => 'kiralik',
                'parent_id' => $altKategoriIds['daire'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 2,
                'icon' => 'rent',
                'description' => 'Kiralık daire'
            ],

            // Villa Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['villa'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık villa'
            ],
            [
                'name' => 'Kiralık',
                'slug' => 'kiralik',
                'parent_id' => $altKategoriIds['villa'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 2,
                'icon' => 'rent',
                'description' => 'Kiralık villa'
            ],

            // Müstakil Ev Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['mustakil-ev'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık müstakil ev'
            ],
            [
                'name' => 'Kiralık',
                'slug' => 'kiralik',
                'parent_id' => $altKategoriIds['mustakil-ev'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 2,
                'icon' => 'rent',
                'description' => 'Kiralık müstakil ev'
            ],

            // Dubleks Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['dubleks'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık dubleks'
            ],
            [
                'name' => 'Kiralık',
                'slug' => 'kiralik',
                'parent_id' => $altKategoriIds['dubleks'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 2,
                'icon' => 'rent',
                'description' => 'Kiralık dubleks'
            ],

            // Ofis Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['ofis'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık ofis'
            ],
            [
                'name' => 'Kiralık',
                'slug' => 'kiralik',
                'parent_id' => $altKategoriIds['ofis'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 2,
                'icon' => 'rent',
                'description' => 'Kiralık ofis'
            ],

            // Dükkan Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['dukkan'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık dükkan'
            ],
            [
                'name' => 'Kiralık',
                'slug' => 'kiralik',
                'parent_id' => $altKategoriIds['dukkan'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 2,
                'icon' => 'rent',
                'description' => 'Kiralık dükkan'
            ],

            // Fabrika Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['fabrika'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık fabrika'
            ],
            [
                'name' => 'Kiralık',
                'slug' => 'kiralik',
                'parent_id' => $altKategoriIds['fabrika'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 2,
                'icon' => 'rent',
                'description' => 'Kiralık fabrika'
            ],

            // Depo Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['depo'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık depo'
            ],
            [
                'name' => 'Kiralık',
                'slug' => 'kiralik',
                'parent_id' => $altKategoriIds['depo'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 2,
                'icon' => 'rent',
                'description' => 'Kiralık depo'
            ],

            // İmar Arsaları Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['imar-arsalari'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık imar arsası'
            ],

            // Tarım Arazileri Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['tarim-arazileri'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık tarım arazisi'
            ],

            // Orman Arazileri Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['orman-arazileri'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık orman arazisi'
            ],

            // Günlük Kiralama Yayın Tipleri
            [
                'name' => 'Günlük',
                'slug' => 'gunluk',
                'parent_id' => $altKategoriIds['gunluk-kiralama'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'calendar-day',
                'description' => 'Günlük yazlık kiralama'
            ],

            // Haftalık Kiralama Yayın Tipleri
            [
                'name' => 'Haftalık',
                'slug' => 'haftalik',
                'parent_id' => $altKategoriIds['haftalik-kiralama'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'calendar-week',
                'description' => 'Haftalık yazlık kiralama'
            ],

            // Aylık Kiralama Yayın Tipleri
            [
                'name' => 'Aylık',
                'slug' => 'aylik',
                'parent_id' => $altKategoriIds['aylik-kiralama'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'calendar-month',
                'description' => 'Aylık yazlık kiralama'
            ],

            // Otel Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['otel'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık otel'
            ],
            [
                'name' => 'Kiralık',
                'slug' => 'kiralik',
                'parent_id' => $altKategoriIds['otel'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 2,
                'icon' => 'rent',
                'description' => 'Kiralık otel'
            ],

            // Pansiyon Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['pansiyon'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık pansiyon'
            ],
            [
                'name' => 'Kiralık',
                'slug' => 'kiralik',
                'parent_id' => $altKategoriIds['pansiyon'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 2,
                'icon' => 'rent',
                'description' => 'Kiralık pansiyon'
            ],

            // Tatil Köyü Yayın Tipleri
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'parent_id' => $altKategoriIds['tatil-koyu'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 1,
                'icon' => 'sale',
                'description' => 'Satılık tatil köyü'
            ],
            [
                'name' => 'Kiralık',
                'slug' => 'kiralik',
                'parent_id' => $altKategoriIds['tatil-koyu'],
                'seviye' => 2,
                'status' => true,
                'display_order' => 2,
                'icon' => 'rent',
                'description' => 'Kiralık tatil köyü'
            ]
        ];

        foreach ($yayinTipleri as $yayinTipi) {
            IlanKategori::create($yayinTipi);
        }

        $this->command->info('İlan kategorileri başarıyla oluşturuldu!');
        $this->command->info('Ana Kategoriler: ' . count($anaKategoriler));
        $this->command->info('Alt Kategoriler: ' . count($altKategoriler));
        $this->command->info('Yayın Tipleri: ' . count($yayinTipleri));
    }
}
