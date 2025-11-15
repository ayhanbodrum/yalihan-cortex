<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\IlanKategori;
use Illuminate\Support\Str;

/**
 * Context7 İlan Kategori Seeder
 * 
 * Context7 MCP Uyumlu Kategori Sistemi
 * 
 * Yapı:
 * - Ana Kategoriler (seviye=0): Konut, Arsa
 * - Alt Kategoriler (seviye=1): Konut altında (Daire, Villa, vb.), Arsa altında (İmarlı Arsa, Tarla, vb.)
 * - Yayın Tipleri (seviye=2): Satılık, Kiralık, Yazlık Kiralık
 * 
 * Context7 Kuralları:
 * - ✅ status field kullanımı (aktif/is_active YASAK)
 * - ✅ display_order kullanımı (order YASAK)
 * - ✅ name kullanımı (ad YASAK)
 * - ✅ seviye field: 0=Ana, 1=Alt, 2=Yayın Tipi
 * 
 * @version Context7 v5.2.0
 * @date 2025-11-11
 */
class Context7IlanKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📂 Context7: İlan Kategorileri seed ediliyor...');

        // Context7: status kolonu kontrolü
        $hasStatusColumn = Schema::hasColumn('ilan_kategorileri', 'status');
        $hasSeviyeColumn = Schema::hasColumn('ilan_kategorileri', 'seviye');

        if (!$hasStatusColumn) {
            $this->command->warn('⚠️ status kolonu yok! Varsayılan değer kullanılacak.');
        }

        if (!$hasSeviyeColumn) {
            $this->command->warn('⚠️ seviye kolonu yok! Varsayılan değer kullanılacak.');
        }

        // ======================================================================
        // 1. ANA KATEGORİLER (Seviye 0)
        // ======================================================================
        $this->command->info('📁 Ana kategoriler oluşturuluyor...');

        $anaKategoriler = [
            [
                'name' => 'Konut',
                'slug' => 'konut',
                'seviye' => 0,
                'display_order' => 1,
                'icon' => 'home',
                'aciklama' => 'Daire, villa, müstakil ev gibi konut türleri',
            ],
            [
                'name' => 'Arsa',
                'slug' => 'arsa',
                'seviye' => 0,
                'display_order' => 2,
                'icon' => 'map',
                'aciklama' => 'İmar, tarım, orman arazileri',
            ],
        ];

        $anaKategoriIds = [];
        foreach ($anaKategoriler as $kategoriData) {
            $data = [
                'name' => $kategoriData['name'],
                'slug' => $kategoriData['slug'],
                'parent_id' => null,
                'seviye' => $kategoriData['seviye'],
                'display_order' => $kategoriData['display_order'],
                'icon' => $kategoriData['icon'] ?? null,
                'aciklama' => $kategoriData['aciklama'] ?? null,
            ];

            // Context7: status kolonu varsa ekle
            if ($hasStatusColumn) {
                $data['status'] = true;
            }

            $kategori = IlanKategori::updateOrCreate(
                ['slug' => $kategoriData['slug']],
                $data
            );

            $anaKategoriIds[$kategoriData['slug']] = $kategori->id;
            $this->command->info("   ✓ {$kategoriData['name']} (ID: {$kategori->id})");
        }

        $this->command->info('   ✅ ' . count($anaKategoriler) . ' ana kategori oluşturuldu');

        // ======================================================================
        // 2. ALT KATEGORİLER (Seviye 1)
        // ======================================================================
        $this->command->info('📁 Alt kategoriler oluşturuluyor...');

        $altKategoriler = [
            // Konut Alt Kategorileri
            [
                'name' => 'Daire',
                'slug' => 'daire',
                'parent_slug' => 'konut',
                'display_order' => 1,
                'icon' => 'building',
                'aciklama' => 'Apartman daireleri, rezidans daireleri',
            ],
            [
                'name' => 'Villa',
                'slug' => 'villa',
                'parent_slug' => 'konut',
                'display_order' => 2,
                'icon' => 'house',
                'aciklama' => 'Müstakil villalar, lüks konutlar',
            ],
            [
                'name' => 'Müstakil Ev',
                'slug' => 'mustakil-ev',
                'parent_slug' => 'konut',
                'display_order' => 3,
                'icon' => 'home',
                'aciklama' => 'Tek katlı veya çok katlı müstakil evler',
            ],
            [
                'name' => 'Residence',
                'slug' => 'residence',
                'parent_slug' => 'konut',
                'display_order' => 4,
                'icon' => 'building-office',
                'aciklama' => 'Lüks rezidans projeleri',
            ],
            [
                'name' => 'Yazlık',
                'slug' => 'yazlik',
                'parent_slug' => 'konut',
                'display_order' => 5,
                'icon' => 'sun',
                'aciklama' => 'Yazlık konutlar, tatil evleri',
            ],
            [
                'name' => 'Çiftlik Evi',
                'slug' => 'ciftlik-evi',
                'parent_slug' => 'konut',
                'display_order' => 6,
                'icon' => 'tractor',
                'aciklama' => 'Çiftlik evleri, köy evleri',
            ],
            [
                'name' => 'Köşk',
                'slug' => 'kosk',
                'parent_slug' => 'konut',
                'display_order' => 7,
                'icon' => 'crown',
                'aciklama' => 'Tarihi köşkler, konaklar',
            ],
            [
                'name' => 'Apart',
                'slug' => 'apart',
                'parent_slug' => 'konut',
                'display_order' => 8,
                'icon' => 'building-office-2',
                'aciklama' => 'Apart daireleri',
            ],

            // Arsa Alt Kategorileri
            [
                'name' => 'İmarlı Arsa',
                'slug' => 'imarli-arsa',
                'parent_slug' => 'arsa',
                'display_order' => 1,
                'icon' => 'map-pin',
                'aciklama' => 'İmar planında yapılaşmaya uygun arsa',
            ],
            [
                'name' => 'Tarla',
                'slug' => 'tarla',
                'parent_slug' => 'arsa',
                'display_order' => 2,
                'icon' => 'tractor',
                'aciklama' => 'Tarımsal üretim için kullanılan arazi',
            ],
            [
                'name' => 'Bağ',
                'slug' => 'bag',
                'parent_slug' => 'arsa',
                'display_order' => 3,
                'icon' => 'grapes',
                'aciklama' => 'Bağ arazileri',
            ],
            [
                'name' => 'Bahçe',
                'slug' => 'bahce',
                'parent_slug' => 'arsa',
                'display_order' => 4,
                'icon' => 'tree',
                'aciklama' => 'Bahçe arazileri',
            ],
            [
                'name' => 'Zeytinlik',
                'slug' => 'zeytinlik',
                'parent_slug' => 'arsa',
                'display_order' => 5,
                'icon' => 'olive',
                'aciklama' => 'Zeytin ağaçları bulunan tarımsal arazi',
            ],
            [
                'name' => 'Turistik Arsa',
                'slug' => 'turistik-arsa',
                'parent_slug' => 'arsa',
                'display_order' => 6,
                'icon' => 'map',
                'aciklama' => 'Turizm amaçlı arsa',
            ],
            [
                'name' => 'Orman Arazisi',
                'slug' => 'orman-arazisi',
                'parent_slug' => 'arsa',
                'display_order' => 7,
                'icon' => 'tree-pine',
                'aciklama' => 'Orman arazileri',
            ],
            [
                'name' => 'Mera',
                'slug' => 'mera',
                'parent_slug' => 'arsa',
                'display_order' => 8,
                'icon' => 'grass',
                'aciklama' => 'Mera arazileri',
            ],
        ];

        $altKategoriIds = [];
        foreach ($altKategoriler as $kategoriData) {
            $parentSlug = $kategoriData['parent_slug'];
            $parentId = $anaKategoriIds[$parentSlug] ?? null;

            if (!$parentId) {
                $this->command->warn("   ⚠️ Parent kategori bulunamadı: {$parentSlug}");
                continue;
            }

            $data = [
                'name' => $kategoriData['name'],
                'slug' => $kategoriData['slug'],
                'parent_id' => $parentId,
                'seviye' => 1, // Alt kategori
                'display_order' => $kategoriData['display_order'],
                'icon' => $kategoriData['icon'] ?? null,
                'aciklama' => $kategoriData['aciklama'] ?? null,
            ];

            // Context7: status kolonu varsa ekle
            if ($hasStatusColumn) {
                $data['status'] = true;
            }

            $kategori = IlanKategori::updateOrCreate(
                ['slug' => $kategoriData['slug']],
                $data
            );

            $altKategoriIds[$kategoriData['slug']] = $kategori->id;
            $this->command->info("   ✓ {$kategoriData['name']} (ID: {$kategori->id}, Parent: {$parentSlug})");
        }

        $this->command->info('   ✅ ' . count($altKategoriler) . ' alt kategori oluşturuldu');

        // ======================================================================
        // 3. YAYIN TİPLERİ (Seviye 2)
        // ======================================================================
        $this->command->info('📁 Yayın tipleri oluşturuluyor...');

        // Yayın tipleri ana kategoriye bağlı değil, genel olarak kullanılır
        // Ancak parent_id olarak ana kategorilerden birini kullanabiliriz
        // Veya null bırakabiliriz (seviye=2 olduğu için)

        $yayinTipleri = [
            [
                'name' => 'Satılık',
                'slug' => 'satilik',
                'display_order' => 1,
                'icon' => 'tag',
                'aciklama' => 'Satılık ilanlar',
            ],
            [
                'name' => 'Kiralık',
                'slug' => 'kiralik',
                'display_order' => 2,
                'icon' => 'key',
                'aciklama' => 'Kiralık ilanlar',
            ],
            [
                'name' => 'Yazlık Kiralık',
                'slug' => 'yazlik-kiralik',
                'display_order' => 3,
                'icon' => 'sun',
                'aciklama' => 'Yazlık kiralık ilanlar (günlük, haftalık, aylık)',
            ],
        ];

        $yayinTipiIds = [];
        foreach ($yayinTipleri as $yayinTipiData) {
            $data = [
                'name' => $yayinTipiData['name'],
                'slug' => $yayinTipiData['slug'],
                'parent_id' => null, // Yayın tipleri genel olarak kullanılır
                'seviye' => 2, // Yayın tipi
                'display_order' => $yayinTipiData['display_order'],
                'icon' => $yayinTipiData['icon'] ?? null,
                'aciklama' => $yayinTipiData['aciklama'] ?? null,
            ];

            // Context7: status kolonu varsa ekle
            if ($hasStatusColumn) {
                $data['status'] = true;
            }

            $kategori = IlanKategori::updateOrCreate(
                ['slug' => $yayinTipiData['slug']],
                $data
            );

            $yayinTipiIds[$yayinTipiData['slug']] = $kategori->id;
            $this->command->info("   ✓ {$yayinTipiData['name']} (ID: {$kategori->id})");
        }

        $this->command->info('   ✅ ' . count($yayinTipleri) . ' yayın tipi oluşturuldu');

        // ======================================================================
        // ÖZET
        // ======================================================================
        $this->command->info('');
        $this->command->info('✅ Context7 Kategori Sistemi tamamlandı!');
        $this->command->info('');
        $this->command->info('📊 İstatistikler:');
        $this->command->info('   • Ana Kategoriler: ' . IlanKategori::where('seviye', 0)->count());
        $this->command->info('   • Alt Kategoriler: ' . IlanKategori::where('seviye', 1)->count());
        $this->command->info('   • Yayın Tipleri: ' . IlanKategori::where('seviye', 2)->count());
        $this->command->info('   • Toplam: ' . IlanKategori::count());
        $this->command->info('');
        $this->command->info('🎯 Context7 Uyumluluk:');
        $this->command->info('   ✅ status field kullanımı');
        $this->command->info('   ✅ display_order kullanımı');
        $this->command->info('   ✅ name field kullanımı');
        $this->command->info('   ✅ seviye field kullanımı (0=Ana, 1=Alt, 2=Yayın Tipi)');
    }
}

