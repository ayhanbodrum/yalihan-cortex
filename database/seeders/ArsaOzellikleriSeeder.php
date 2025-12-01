<?php

namespace Database\Seeders;

use App\Models\Ozellik;
use App\Models\OzellikKategori;
use Illuminate\Database\Seeder;

class ArsaOzellikleriSeeder extends Seeder
{
    /**
     * Arsa Özellikleri - Kapsamlı Tanımlamalar
     * Context7 Compliant - 2025-10-23
     */
    public function run(): void
    {
        $this->command->info("🏞️ Arsa özellikleri oluşturuluyor...\n");

        // ✅ Arsa Özellik Kategorileri (4 ana grup)
        // NOT: Table'da 'display_order' column var, model'de 'sira' kullanılıyor
        $arsaKategorileri = [
            [
                'name' => 'İmar ve Yapılaşma',
                'slug' => 'imar-yapilasma',
                'aciklama' => 'İmar durumu, yapılaşma katsayıları ve imar planı özellikleri',
                'icon' => 'building-circle-check',
                'display_order' => 10,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Parsel Bilgileri',
                'slug' => 'parsel-bilgileri',
                'aciklama' => 'Ada, parsel, tapu ve sınır bilgileri',
                'icon' => 'map-location-dot',
                'display_order' => 11,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Altyapı ve Tesisatlar',
                'slug' => 'altyapi-tesisat',
                'aciklama' => 'Elektrik, su, doğalgaz ve diğer altyapı özellikleri',
                'icon' => 'plug',
                'display_order' => 12,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Tarımsal Özellikler',
                'slug' => 'tarimsal-ozellikler',
                'aciklama' => 'Sulama, ürün tipi, ağaç sayısı ve tarımsal kullanım özellikleri',
                'icon' => 'seedling',
                'display_order' => 13,
                'status' => 'Aktif',
            ],
        ];

        foreach ($arsaKategorileri as $kategori) {
            OzellikKategori::updateOrCreate(
                ['slug' => $kategori['slug']],
                $kategori
            );
            $this->command->info("✅ Kategori: {$kategori['name']}");
        }

        // ✅ İmar ve Yapılaşma Özellikleri
        $imarKategori = OzellikKategori::where('slug', 'imar-yapilaşma')->first();

        if ($imarKategori) {
            $imarOzellikleri = [
                [
                    'name' => 'İmar Durumu',
                    'slug' => 'imar-durumu',
                    'kategori_id' => $imarKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['İmarlı', 'İmarsız', 'Tarla', 'Konut İmarlı', 'Villa İmarlı', 'Ticari İmarlı', 'Turizm İmarlı', 'Sanayi İmarlı']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Arsanın imar planındaki durumu',
                    'display_order' => 1,
                ],
                [
                    'name' => 'KAKS',
                    'slug' => 'kaks',
                    'kategori_id' => $imarKategori->id,
                    'veri_tipi' => 'decimal',
                    'birim' => 'kat',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Kat Alanı Kat Sayısı (Floor Area Ratio)',
                    'display_order' => 2,
                ],
                [
                    'name' => 'TAKS',
                    'slug' => 'taks',
                    'kategori_id' => $imarKategori->id,
                    'veri_tipi' => 'decimal',
                    'birim' => 'oran',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Taban Alanı Kat Sayısı (Building Coverage Ratio)',
                    'display_order' => 3,
                ],
                [
                    'name' => 'Gabari',
                    'slug' => 'gabari',
                    'kategori_id' => $imarKategori->id,
                    'veri_tipi' => 'decimal',
                    'birim' => 'metre',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Maksimum bina yüksekliği (m)',
                    'display_order' => 4,
                ],
                [
                    'name' => 'İmar Planı',
                    'slug' => 'imar-plani',
                    'kategori_id' => $imarKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'İmar planı belgesi mevcut mu?',
                    'display_order' => 5,
                ],
            ];

            foreach ($imarOzellikleri as $ozellik) {
                Ozellik::updateOrCreate(
                    ['slug' => $ozellik['slug']],
                    $ozellik
                );
            }
            $this->command->info('   → 5 imar özelliği eklendi');
        }

        // ✅ Parsel Bilgileri Özellikleri
        $parselKategori = OzellikKategori::where('slug', 'parsel-bilgileri')->first();

        if ($parselKategori) {
            $parselOzellikleri = [
                [
                    'name' => 'Ada No',
                    'slug' => 'ada-no',
                    'kategori_id' => $parselKategori->id,
                    'veri_tipi' => 'text',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Tapuda kayıtlı ada numarası',
                    'display_order' => 1,
                ],
                [
                    'name' => 'Parsel No',
                    'slug' => 'parsel-no',
                    'kategori_id' => $parselKategori->id,
                    'veri_tipi' => 'text',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Tapuda kayıtlı parsel numarası',
                    'display_order' => 2,
                ],
                [
                    'name' => 'Tapu Durumu',
                    'slug' => 'tapu-durumu',
                    'kategori_id' => $parselKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Tek Tapu', 'Kat Mülkiyetli', 'Kat İrtifaklı', 'Arsa Paylı', 'Hisseli']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Tapunun hukuki durumu',
                    'display_order' => 3,
                ],
                [
                    'name' => 'Yola Cephe',
                    'slug' => 'yola-cephe',
                    'kategori_id' => $parselKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Arsanın yola cephesi var mı?',
                    'display_order' => 4,
                ],
                [
                    'name' => 'Cephe Mesafesi',
                    'slug' => 'cephe-mesafesi',
                    'kategori_id' => $parselKategori->id,
                    'veri_tipi' => 'decimal',
                    'birim' => 'metre',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Yola cephe mesafesi (m)',
                    'display_order' => 5,
                ],
                [
                    'name' => 'Köşe Parsel',
                    'slug' => 'kose-parsel',
                    'kategori_id' => $parselKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Köşe parselde mi?',
                    'display_order' => 6,
                ],
            ];

            foreach ($parselOzellikleri as $ozellik) {
                Ozellik::updateOrCreate(
                    ['slug' => $ozellik['slug']],
                    $ozellik
                );
            }
            $this->command->info('   → 6 parsel özelliği eklendi');
        }

        // ✅ Altyapı ve Tesisat Özellikleri
        $altyapiKategori = OzellikKategori::where('slug', 'altyapi-tesisat')->first();

        if ($altyapiKategori) {
            $altyapiOzellikleri = [
                [
                    'name' => 'Elektrik',
                    'slug' => 'elektrik',
                    'kategori_id' => $altyapiKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Elektrik altyapısı mevcut',
                    'display_order' => 1,
                ],
                [
                    'name' => 'Su',
                    'slug' => 'su',
                    'kategori_id' => $altyapiKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Su altyapısı mevcut',
                    'display_order' => 2,
                ],
                [
                    'name' => 'Doğalgaz',
                    'slug' => 'dogalgaz',
                    'kategori_id' => $altyapiKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Doğalgaz altyapısı mevcut',
                    'display_order' => 3,
                ],
                [
                    'name' => 'Kanalizasyon',
                    'slug' => 'kanalizasyon',
                    'kategori_id' => $altyapiKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Kanalizasyon sistemi mevcut',
                    'display_order' => 4,
                ],
                [
                    'name' => 'Telefon Hattı',
                    'slug' => 'telefon-hatti',
                    'kategori_id' => $altyapiKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Telefon hattı altyapısı mevcut',
                    'display_order' => 5,
                ],
                [
                    'name' => 'Fiber İnternet',
                    'slug' => 'fiber-internet',
                    'kategori_id' => $altyapiKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Fiber internet altyapısı mevcut',
                    'display_order' => 6,
                ],
            ];

            foreach ($altyapiOzellikleri as $ozellik) {
                Ozellik::updateOrCreate(
                    ['slug' => $ozellik['slug']],
                    $ozellik
                );
            }
            $this->command->info('   → 6 altyapı özelliği eklendi');
        }

        // ✅ Tarımsal Özellikler
        $tarimKategori = OzellikKategori::where('slug', 'tarimsal-ozellikler')->first();

        if ($tarimKategori) {
            $tarimOzellikleri = [
                [
                    'name' => 'Sulama Sistemi',
                    'slug' => 'sulama-sistemi',
                    'kategori_id' => $tarimKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Yok', 'Damlama', 'Yağmurlama', 'Salma', 'Kuyu', 'Kanal']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Sulama sistemi tipi',
                    'display_order' => 1,
                ],
                [
                    'name' => 'Zeytin Ağacı Sayısı',
                    'slug' => 'zeytin-agac-sayisi',
                    'kategori_id' => $tarimKategori->id,
                    'veri_tipi' => 'number',
                    'birim' => 'adet',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Zeytinlikte bulunan ağaç sayısı',
                    'display_order' => 2,
                ],
                [
                    'name' => 'Ağaç Yaşı',
                    'slug' => 'agac-yasi',
                    'kategori_id' => $tarimKategori->id,
                    'veri_tipi' => 'number',
                    'birim' => 'yıl',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Ağaçların ortalama yaşı',
                    'display_order' => 3,
                ],
                [
                    'name' => 'Toprak Tipi',
                    'slug' => 'toprak-tipi',
                    'kategori_id' => $tarimKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Kırmızı Toprak', 'Killi', 'Kumlu', 'Alüvyonlu', 'Taşlı', 'Verimli']),
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Toprak yapısı ve özellikleri',
                    'display_order' => 4,
                ],
                [
                    'name' => 'Ürün Tipi',
                    'slug' => 'urun-tipi',
                    'kategori_id' => $tarimKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Zeytin', 'Üzüm', 'Narenciye', 'Sebze', 'Meyve', 'Tahıl', 'Diğer']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Yetiştirilen ürün türü',
                    'display_order' => 5,
                ],
                [
                    'name' => 'Yıllık Verim',
                    'slug' => 'yillik-verim',
                    'kategori_id' => $tarimKategori->id,
                    'veri_tipi' => 'number',
                    'birim' => 'ton',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Yıllık ortalama ürün verimi (ton)',
                    'display_order' => 6,
                ],
                [
                    'name' => 'Sera',
                    'slug' => 'sera',
                    'kategori_id' => $tarimKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Sera mevcut mu?',
                    'display_order' => 7,
                ],
                [
                    'name' => 'Ahır',
                    'slug' => 'ahir',
                    'kategori_id' => $tarimKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Hayvan ahırı mevcut mu?',
                    'display_order' => 8,
                ],
                [
                    'name' => 'Ağıl',
                    'slug' => 'agil',
                    'kategori_id' => $tarimKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Küçükbaş hayvan ağılı mevcut mu?',
                    'display_order' => 9,
                ],
                [
                    'name' => 'Su Kuyusu',
                    'slug' => 'su-kuyusu',
                    'kategori_id' => $tarimKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Yeraltı su kuyusu mevcut mu?',
                    'display_order' => 10,
                ],
            ];

            foreach ($tarimOzellikleri as $ozellik) {
                Ozellik::updateOrCreate(
                    ['slug' => $ozellik['slug']],
                    $ozellik
                );
            }
            $this->command->info('   → 10 tarımsal özellik eklendi');
        }

        $this->command->info("\n📊 ARSA ÖZELLİKLERİ RAPORU:");
        $this->command->info('   ✅ Özellik Kategorisi: 4');
        $this->command->info('   ✅ Toplam Özellik: 21');
        $this->command->info('   🎯 İmar ve Yapılaşma: 5');
        $this->command->info('   🎯 Parsel Bilgileri: 6');
        $this->command->info('   🎯 Altyapı: 6');
        $this->command->info('   🎯 Tarımsal: 10');
    }
}
