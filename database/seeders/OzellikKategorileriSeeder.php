<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OzellikKategorileriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🏗️ AI-DESTEKLİ 4D MATRİX SİSTEMİ - ÖZELLİK KATEGORİLERİ\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        // 1. Ana Özellik Kategorileri
        $this->seedOzellikKategorileri();

        // 2. Alt Özellikler
        $this->seedAltOzellikler();

        // 3. 4D Matrix (Kategori × Yayın Tipi × Özellik Kategorisi × Alt Özellik)
        $this->seed4DMatrix();

        echo "🎉 AI-DESTEKLİ 4D MATRİX SİSTEMİ HAZIR!\n";
    }

    private function seedOzellikKategorileri(): void
    {
        echo "📊 Ana Özellik Kategorileri oluşturuluyor...\n";

        $kategoriler = [
            ['name' => 'Altyapı', 'slug' => 'altyapi', 'icon' => '🏗️', 'order' => 1],
            ['name' => 'Genel Özellikler', 'slug' => 'genel_ozellikler', 'icon' => '🌳', 'order' => 2],
            ['name' => 'Manzara', 'slug' => 'manzara', 'icon' => '🏔️', 'order' => 3],
            ['name' => 'Konum', 'slug' => 'konum', 'icon' => '📍', 'order' => 4],
        ];

        foreach($kategoriler as $kategori) {
            DB::table('ozellik_kategorileri')->updateOrInsert(
                ['slug' => $kategori['slug']],
                array_merge($kategori, [
                    'aciklama' => $kategori['name'] . ' ile ilgili özellikler',
                    'parent_id' => null,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        echo "✅ " . count($kategoriler) . " ana kategori oluşturuldu\n\n";
    }

    private function seedAltOzellikler(): void
    {
        echo "🔧 Alt Özellikler oluşturuluyor...\n";

        // Altyapı alt özellikleri
        $altyapiOzellikleri = [
            ['alt_kategori_adi' => 'Elektrik', 'alt_kategori_slug' => 'elektrik', 'alt_kategori_icon' => '⚡', 'field_type' => 'select', 'field_options' => json_encode(['Var', 'Yok', 'Sanayi Elektriği']), 'field_unit' => null],
            ['alt_kategori_adi' => 'Su', 'alt_kategori_slug' => 'su', 'alt_kategori_icon' => '💧', 'field_type' => 'select', 'field_options' => json_encode(['Şehir Suyu', 'Kuyu', 'Sondaj', 'Yok']), 'field_unit' => null],
            ['alt_kategori_adi' => 'Telefon', 'alt_kategori_slug' => 'telefon', 'alt_kategori_icon' => '📞', 'field_type' => 'boolean', 'field_options' => null, 'field_unit' => null],
            ['alt_kategori_adi' => 'Doğalgaz', 'alt_kategori_slug' => 'dogalgaz', 'alt_kategori_icon' => '🔥', 'field_type' => 'boolean', 'field_options' => null, 'field_unit' => null],
            ['alt_kategori_adi' => 'Kanalizasyon', 'alt_kategori_slug' => 'kanalizasyon', 'alt_kategori_icon' => '🚰', 'field_type' => 'boolean', 'field_options' => null, 'field_unit' => null],
            ['alt_kategori_adi' => 'Yol', 'alt_kategori_slug' => 'yol', 'alt_kategori_icon' => '🛣️', 'field_type' => 'select', 'field_options' => json_encode(['Açılmış', 'Açılmamış', 'Yok']), 'field_unit' => null],
        ];

        // Genel Özellikler alt özellikleri
        $genelOzellikleri = [
            ['alt_kategori_adi' => 'Bahçe', 'alt_kategori_slug' => 'bahce', 'alt_kategori_icon' => '🌳', 'field_type' => 'select', 'field_options' => json_encode(['Var', 'Yok', 'Büyük', 'Küçük']), 'field_unit' => 'm²'],
            ['alt_kategori_adi' => 'Havuz', 'alt_kategori_slug' => 'havuz', 'alt_kategori_icon' => '🏊', 'field_type' => 'select', 'field_options' => json_encode(['Var', 'Yok', 'Özel', 'Ortak']), 'field_unit' => null],
            ['alt_kategori_adi' => 'Otopark', 'alt_kategori_slug' => 'otopark', 'alt_kategori_icon' => '🚗', 'field_type' => 'select', 'field_options' => json_encode(['Var', 'Yok', 'Kapalı', 'Açık']), 'field_unit' => null],
            ['alt_kategori_adi' => 'Güvenlik', 'alt_kategori_slug' => 'guvenlik', 'alt_kategori_icon' => '🔒', 'field_type' => 'select', 'field_options' => json_encode(['Var', 'Yok', '7/24', 'Gündüz']), 'field_unit' => null],
            ['alt_kategori_adi' => 'Asansör', 'alt_kategori_slug' => 'asansor', 'alt_kategori_icon' => '🛗', 'field_type' => 'boolean', 'field_options' => null, 'field_unit' => null],
        ];

        // Manzara alt özellikleri
        $manzaraOzellikleri = [
            ['alt_kategori_adi' => 'Deniz', 'alt_kategori_slug' => 'deniz', 'alt_kategori_icon' => '🌊', 'field_type' => 'select', 'field_options' => json_encode(['Var', 'Yok', 'Yakın', 'Uzak']), 'field_unit' => 'km'],
            ['alt_kategori_adi' => 'Dağ', 'alt_kategori_slug' => 'dag', 'alt_kategori_icon' => '🏔️', 'field_type' => 'select', 'field_options' => json_encode(['Var', 'Yok', 'Yakın', 'Uzak']), 'field_unit' => 'km'],
            ['alt_kategori_adi' => 'Şehir', 'alt_kategori_slug' => 'sehir', 'alt_kategori_icon' => '🏙️', 'field_type' => 'select', 'field_options' => json_encode(['Var', 'Yok', 'Yakın', 'Uzak']), 'field_unit' => 'km'],
            ['alt_kategori_adi' => 'Doğa', 'alt_kategori_slug' => 'doga', 'alt_kategori_icon' => '🌲', 'field_type' => 'select', 'field_options' => json_encode(['Orman', 'Göl', 'Park', 'Yok']), 'field_unit' => null],
        ];

        // Konum alt özellikleri
        $konumOzellikleri = [
            ['alt_kategori_adi' => 'Merkezi', 'alt_kategori_slug' => 'merkezi', 'alt_kategori_icon' => '🏛️', 'field_type' => 'select', 'field_options' => json_encode(['Var', 'Yok', 'Yakın', 'Uzak']), 'field_unit' => 'km'],
            ['alt_kategori_adi' => 'Ulaşım', 'alt_kategori_slug' => 'ulasim', 'alt_kategori_icon' => '🚌', 'field_type' => 'select', 'field_options' => json_encode(['Metro', 'Otobüs', 'Minibüs', 'Yok']), 'field_unit' => null],
            ['alt_kategori_adi' => 'Okul', 'alt_kategori_slug' => 'okul', 'alt_kategori_icon' => '🎓', 'field_type' => 'select', 'field_options' => json_encode(['Var', 'Yok', 'Yakın', 'Uzak']), 'field_unit' => 'km'],
            ['alt_kategori_adi' => 'Hastane', 'alt_kategori_slug' => 'hastane', 'alt_kategori_icon' => '🏥', 'field_type' => 'select', 'field_options' => json_encode(['Var', 'Yok', 'Yakın', 'Uzak']), 'field_unit' => 'km'],
            ['alt_kategori_adi' => 'Alışveriş', 'alt_kategori_slug' => 'alisveris', 'alt_kategori_icon' => '🛒', 'field_type' => 'select', 'field_options' => json_encode(['AVM', 'Market', 'Bakkal', 'Yok']), 'field_unit' => null],
        ];

        $allOzellikleri = [
            'altyapi' => $altyapiOzellikleri,
            'genel_ozellikler' => $genelOzellikleri,
            'manzara' => $manzaraOzellikleri,
            'konum' => $konumOzellikleri,
        ];

        foreach($allOzellikleri as $kategoriSlug => $ozellikleri) {
            $kategoriId = DB::table('ozellik_kategorileri')->where('slug', $kategoriSlug)->value('id');

            foreach($ozellikleri as $index => $ozellik) {
                DB::table('ozellikler')->updateOrInsert(
                    ['slug' => $ozellik['alt_kategori_slug']],
                    [
                        'kategori_id' => $kategoriId,
                        'name' => $ozellik['alt_kategori_adi'],
                        'slug' => $ozellik['alt_kategori_slug'],
                        'veri_tipi' => $ozellik['field_type'],
                        'veri_secenekleri' => $ozellik['field_options'],
                        'birim' => $ozellik['field_unit'],
                        'status' => 1,
                        'order' => $index + 1,
                        'zorunlu' => 0,
                        'arama_filtresi' => 1,
                        'ilan_kartinda_goster' => 1,
                        'aciklama' => $ozellik['alt_kategori_adi'] . ' özelliği',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }

        echo "✅ " . array_sum(array_map('count', $allOzellikleri)) . " alt özellik oluşturuldu\n\n";
    }

    private function seed4DMatrix(): void
    {
        echo "🎯 4D Matrix oluşturuluyor...\n";

        $kategoriler = ['konut', 'arsa', 'yazlik', 'isyeri'];
        $yayinTipleri = ['Satılık', 'Kiralık', 'Sezonluk Kiralık', 'Devren Satış'];

        $matrixData = [];

        foreach($kategoriler as $kategoriSlug) {
            foreach($yayinTipleri as $yayinTipi) {
                // Her kategori için uygun özellikleri seç
                $ozellikler = $this->getKategoriOzellikleri($kategoriSlug, $yayinTipi);

                foreach($ozellikler as $ozellik) {
                    $matrixData[] = [
                        'kategori_slug' => $kategoriSlug,
                        'yayin_tipi' => $yayinTipi,
                        'ozellik_kategori_id' => $ozellik['kategori_id'],
                        'ozellik_alt_kategori_id' => $ozellik['ozellik_id'],
                        'aktif' => $ozellik['aktif'],
                        'zorunlu' => $ozellik['zorunlu'],
                        'ai_suggestion' => $ozellik['ai_suggestion'],
                        'ai_auto_fill' => $ozellik['ai_auto_fill'],
                        'sira' => $ozellik['sira'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
        }

        // Mevcut matrix tablosu yoksa oluştur
        if (!Schema::hasTable('kategori_ozellik_matrix')) {
            echo "⚠️  Matrix tablosu bulunamadı, mevcut yapı kullanılıyor\n";
            return;
        }

        DB::table('kategori_ozellik_matrix')->insert($matrixData);
        echo "✅ " . count($matrixData) . " matrix kombinasyonu oluşturuldu\n\n";
    }

    private function getKategoriOzellikleri($kategoriSlug, $yayinTipi): array
    {
        // Kategori bazlı özellik tanımları
        $kategoriOzellikleri = [
            'konut' => [
                'altyapi' => ['elektrik', 'su', 'dogalgaz', 'telefon', 'kanalizasyon'],
                'genel_ozellikler' => ['bahce', 'havuz', 'otopark', 'guvenlik', 'asansor'],
                'manzara' => ['deniz', 'dag', 'sehir'],
                'konum' => ['merkezi', 'ulasim', 'okul', 'hastane', 'alisveris']
            ],
            'arsa' => [
                'altyapi' => ['elektrik', 'su', 'yol'],
                'genel_ozellikler' => ['bahce'],
                'manzara' => ['deniz', 'dag', 'sehir', 'doga'],
                'konum' => ['merkezi', 'ulasim']
            ],
            'yazlik' => [
                'altyapi' => ['elektrik', 'su', 'telefon', 'yol'],
                'genel_ozellikler' => ['bahce', 'havuz', 'otopark', 'guvenlik'],
                'manzara' => ['deniz', 'dag', 'doga'],
                'konum' => ['merkezi', 'ulasim']
            ],
            'isyeri' => [
                'altyapi' => ['elektrik', 'su', 'telefon', 'dogalgaz'],
                'genel_ozellikler' => ['otopark', 'guvenlik', 'asansor'],
                'manzara' => ['sehir'],
                'konum' => ['merkezi', 'ulasim', 'alisveris']
            ]
        ];

        $ozellikler = [];
        $sira = 1;

        foreach($kategoriOzellikleri[$kategoriSlug] as $kategoriSlug => $altOzellikler) {
            $kategoriId = DB::table('ozellik_kategorileri')->where('slug', $kategoriSlug)->value('id');

            foreach($altOzellikler as $altOzellikSlug) {
                $ozellikId = DB::table('ozellikler')->where('slug', $altOzellikSlug)->value('id');

                if($kategoriId && $ozellikId) {
                    $ozellikler[] = [
                        'kategori_id' => $kategoriId,
                        'ozellik_id' => $ozellikId,
                        'aktif' => true,
                        'zorunlu' => in_array($altOzellikSlug, ['elektrik', 'su', 'yol']),
                        'ai_suggestion' => in_array($altOzellikSlug, ['deniz', 'bahce', 'havuz', 'otopark']),
                        'ai_auto_fill' => in_array($altOzellikSlug, ['merkezi', 'ulasim']),
                        'sira' => $sira++
                    ];
                }
            }
        }

        return $ozellikler;
    }
}
