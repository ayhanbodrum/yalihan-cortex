<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;

/**
 * Yazlık Kiralık Ana Kategori Seeder
 *
 * Yazlık Kiralık ana kategori, alt kategoriler ve yayın tiplerini oluşturur.
 *
 * Yapı:
 * - Ana Kategori: Yazlık Kiralık (seviye=0)
 * - Alt Kategoriler: Villa, Daire, Residence, Müstakil Ev, Bungalov, Studio, Apart (seviye=1)
 * - Yayın Tipleri: Günlük, Haftalık, Sezonluk (ana kategoriye bağlı)
 */
class YazlikKiralikAnaKategoriSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏖️ Yazlık Kiralık Ana Kategori Sistemi oluşturuluyor...');

        // Context7: Schema kontrolü
        if (!Schema::hasTable('ilan_kategorileri') || !Schema::hasTable('ilan_kategori_yayin_tipleri')) {
            $this->command->warn('⚠️ Gerekli tablolar bulunamadı!');
            return;
        }

        // 1. Ana Kategori: Yazlık Kiralık
        $this->command->info('  📁 Ana kategori oluşturuluyor...');
        $yazlikAna = IlanKategori::updateOrCreate(
            [
                'name' => 'Yazlık Kiralık',
                'seviye' => 0
            ],
            [
                'slug' => 'yazlik-kiralik',
                'icon' => '🏖️',
                'parent_id' => null,
                'display_order' => 6, // Diğer kategorilerden sonra
                'status' => Schema::hasColumn('ilan_kategorileri', 'status') ? true : null,
                'aciklama' => 'Yazlık kiralık konut ve tesisler'
            ]
        );
        $this->command->info("    ✓ Yazlık Kiralık (ID: {$yazlikAna->id})");

        // 2. Alt Kategoriler
        $this->command->info('  📁 Alt kategoriler oluşturuluyor...');
        $altKategoriler = [
            ['name' => 'Villa', 'slug' => 'yazlik-villa', 'icon' => '🏡', 'display_order' => 1],
            ['name' => 'Daire', 'slug' => 'yazlik-daire', 'icon' => '🏢', 'display_order' => 2],
            ['name' => 'Residence', 'slug' => 'yazlik-residence', 'icon' => '🏘️', 'display_order' => 3],
            ['name' => 'Müstakil Ev', 'slug' => 'yazlik-mustakil-ev', 'icon' => '🏠', 'display_order' => 4],
            ['name' => 'Bungalov', 'slug' => 'yazlik-bungalov', 'icon' => '🏕️', 'display_order' => 5],
            ['name' => 'Studio', 'slug' => 'yazlik-studio', 'icon' => '🏨', 'display_order' => 6],
            ['name' => 'Apart', 'slug' => 'yazlik-apart', 'icon' => '🏬', 'display_order' => 7],
        ];

        $altKategoriIds = [];
        foreach ($altKategoriler as $altKat) {
            $altKategori = IlanKategori::updateOrCreate(
                [
                    'name' => $altKat['name'],
                    'parent_id' => $yazlikAna->id,
                    'seviye' => 1
                ],
                [
                    'slug' => $altKat['slug'],
                    'icon' => $altKat['icon'],
                    'display_order' => $altKat['display_order'],
                    'status' => Schema::hasColumn('ilan_kategorileri', 'status') ? true : null,
                    'aciklama' => "Yazlık kiralık {$altKat['name']}"
                ]
            );
            $altKategoriIds[$altKat['slug']] = $altKategori->id;
            $this->command->info("    ✓ {$altKat['name']} (ID: {$altKategori->id})");
        }

        // 3. Yayın Tipleri (Ana kategoriye bağlı)
        $this->command->info('  📢 Yayın tipleri oluşturuluyor...');
        $yayinTipleri = [
            ['tip' => 'Günlük', 'display_order' => 1],
            ['tip' => 'Haftalık', 'display_order' => 2],
            ['tip' => 'Sezonluk', 'display_order' => 3],
        ];

        $yayinTipiIds = [];
        foreach ($yayinTipleri as $yt) {
            $yayinTipi = IlanKategoriYayinTipi::updateOrCreate(
                [
                    'kategori_id' => $yazlikAna->id,
                    'yayin_tipi' => $yt['tip']
                ],
                [
                    'status' => Schema::hasColumn('ilan_kategori_yayin_tipleri', 'status') ? true : null,
                    'display_order' => $yt['display_order']
                ]
            );
            $yayinTipiIds[$yt['tip']] = $yayinTipi->id;
            $this->command->info("    ✓ {$yt['tip']} (ID: {$yayinTipi->id})");
        }

        // 4. Alt Kategori ↔ Yayın Tipi İlişkileri (Tüm alt kategoriler için tüm yayın tipleri)
        $this->command->info('  🔗 Alt kategori ↔ Yayın tipi ilişkileri oluşturuluyor...');

        if (Schema::hasTable('alt_kategori_yayin_tipi')) {
            $order = 1;
            foreach ($altKategoriIds as $altKatId) {
                foreach ($yayinTipiIds as $ytId) {
                    DB::table('alt_kategori_yayin_tipi')->updateOrInsert(
                        [
                            'alt_kategori_id' => $altKatId,
                            'yayin_tipi_id' => $ytId
                        ],
                        [
                            'enabled' => true,
                            'display_order' => $order++,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                }
            }
            $this->command->info("    ✓ " . (count($altKategoriIds) * count($yayinTipiIds)) . " ilişki oluşturuldu");
        } else {
            $this->command->warn('    ⚠️ alt_kategori_yayin_tipi tablosu bulunamadı!');
        }

        $this->command->info('✅ Yazlık Kiralık Ana Kategori Sistemi tamamlandı!');
        $this->command->info("   📊 Özet: {$yazlikAna->name} → " . count($altKategoriler) . " alt kategori → " . count($yayinTipleri) . " yayın tipi");
    }
}
