<?php

namespace Database\Seeders;

use App\Models\IlanKategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Gemini JSON-Based Category Seeder
 *
 * JSON veri setinden kategorileri ve yayın tiplerini seed eder
 * Source: docs/ai/GEMINI_COMPLETE_SYSTEM_DATA.json v2.0.0
 * Context7: C7-GEMINI-JSON-SEEDER-2025-11-27
 *
 * Context7 Compliance:
 * - ✅ status field (boolean)
 * - ✅ display_order field (integer)
 * - ✅ İngilizce field names
 */
class GeminiJsonBasedCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎯 Gemini JSON-Based Category Seeder başlatılıyor...');
        $this->command->info('📋 Source: docs/ai/GEMINI_COMPLETE_SYSTEM_DATA.json v2.0.0');
        $this->command->info('✅ Context7 Compliance: status, display_order');
        $this->command->newLine();

        // JSON dosyasını yükle
        $jsonPath = base_path('docs/ai/GEMINI_COMPLETE_SYSTEM_DATA.json');

        if (!file_exists($jsonPath)) {
            $this->command->error('❌ JSON dosyası bulunamadı: ' . $jsonPath);
            $this->command->warn('💡 Lütfen docs/ai/GEMINI_COMPLETE_SYSTEM_DATA.json dosyasının var olduğundan emin olun.');

            return;
        }

        $jsonContent = file_get_contents($jsonPath);
        $data = json_decode($jsonContent, true);

        if (!$data || !isset($data['categories']['main_categories'])) {
            $this->command->error('❌ JSON dosyası parse edilemedi veya categories.main_categories bulunamadı.');

            return;
        }

        $this->command->info('✅ JSON dosyası başarıyla yüklendi.');
        $this->command->newLine();

        // Ana kategorileri işle
        $mainCategories = $data['categories']['main_categories'];
        $this->command->info('📦 Ana kategoriler işleniyor: ' . count($mainCategories) . ' adet');

        $anaKategoriIds = [];

        foreach ($mainCategories as $mainCategory) {
            // Ana kategori oluştur veya güncelle
            $anaKategori = IlanKategori::updateOrCreate(
                [
                    'slug' => $mainCategory['slug'],
                    'seviye' => 0,
                ],
                [
                    'name' => $mainCategory['name'],
                    'slug' => $mainCategory['slug'],
                    'seviye' => 0,
                    'status' => true, // Context7: status (boolean)
                    'display_order' => $mainCategory['display_order'], // Context7: display_order
                    'icon' => $mainCategory['icon'],
                    'description' => $mainCategory['description'] ?? null,
                    'parent_id' => null,
                ]
            );

            $anaKategoriIds[$mainCategory['slug']] = $anaKategori->id;

            $this->command->info("  ✅ Ana Kategori: {$mainCategory['name']} (ID: {$anaKategori->id})");

            // Alt kategorileri işle
            if (isset($mainCategory['subcategories']) && is_array($mainCategory['subcategories'])) {
                foreach ($mainCategory['subcategories'] as $subCategory) {
                    // Alt kategori oluştur veya güncelle
                    $altKategori = IlanKategori::updateOrCreate(
                        [
                            'slug' => $subCategory['slug'],
                            'seviye' => 1,
                            'parent_id' => $anaKategori->id,
                        ],
                        [
                            'name' => $subCategory['name'],
                            'slug' => $subCategory['slug'],
                            'seviye' => 1,
                            'status' => true, // Context7: status (boolean)
                            'display_order' => $subCategory['display_order'], // Context7: display_order
                            'icon' => $subCategory['icon'] ?? null,
                            'description' => $subCategory['description'] ?? null,
                            'parent_id' => $anaKategori->id,
                        ]
                    );

                    $this->command->info("    ✅ Alt Kategori: {$subCategory['name']} (ID: {$altKategori->id})");

                    // Yayın tiplerini işle
                    if (isset($subCategory['publication_types']) && is_array($subCategory['publication_types'])) {
                        $this->seedYayinTipleri($altKategori, $subCategory['publication_types']);
                    }
                }
            }
        }

        $this->command->newLine();
        $this->command->info('🎉 Gemini JSON-Based Category Seeder tamamlandı!');
        $this->command->info("📊 Toplam Ana Kategori: " . count($anaKategoriIds));
    }

    /**
     * Yayın tiplerini seed et
     * Context7: ilan_kategori_yayin_tipleri tablosuna kaydet
     *
     * Not: Yayın tipleri alt kategori (seviye 1) ile ilişkilendirilir
     */
    private function seedYayinTipleri(IlanKategori $altKategori, array $publicationTypes): void
    {
        // ilan_kategori_yayin_tipleri tablosu için model kontrolü
        if (!class_exists(\App\Models\IlanKategoriYayinTipi::class)) {
            $this->command->warn("    ⚠️ IlanKategoriYayinTipi model bulunamadı. Yayın tipleri atlanıyor.");

            return;
        }

        // Context7: Schema kontrolü - status kolonu tipini kontrol et
        $hasStatusColumn = \Illuminate\Support\Facades\Schema::hasColumn('ilan_kategori_yayin_tipleri', 'status');
        $statusColumnType = null;

        if ($hasStatusColumn) {
            $statusColumnType = \Illuminate\Support\Facades\DB::selectOne(
                "SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME = 'ilan_kategori_yayin_tipleri'
                 AND COLUMN_NAME = 'status'"
            );
        }

        foreach ($publicationTypes as $index => $pubType) {
            $data = [
                'kategori_id' => $altKategori->id,
                'yayin_tipi' => $pubType['name'],
                'display_order' => $index + 1, // Context7: display_order
            ];

            // Context7: Status field - boolean veya string olabilir
            if ($hasStatusColumn) {
                if ($statusColumnType && $statusColumnType->DATA_TYPE === 'tinyint') {
                    $data['status'] = true; // Boolean (TINYINT(1))
                } else {
                    $data['status'] = 'Aktif'; // String (VARCHAR) - Backward compatibility
                }
            }

            // Yayın tipini oluştur veya güncelle
            \App\Models\IlanKategoriYayinTipi::updateOrCreate(
                [
                    'kategori_id' => $altKategori->id,
                    'yayin_tipi' => $pubType['name'],
                ],
                $data
            );

            $this->command->info("      ✅ Yayın Tipi: {$pubType['name']}");
        }
    }
}
