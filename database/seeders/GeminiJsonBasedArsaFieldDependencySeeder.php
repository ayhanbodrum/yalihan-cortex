<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Gemini JSON-Based Arsa Field Dependency Seeder
 *
 * JSON veri setinden Arsa kategorisi için field dependencies'leri seed eder
 * Source: docs/ai/GEMINI_COMPLETE_SYSTEM_DATA.json v2.0.0
 * Context7: C7-GEMINI-ARSA-FIELD-SEEDER-2025-11-27
 *
 * Context7 Compliance:
 * - ✅ status field (boolean)
 * - ✅ display_order field (integer)
 * - ✅ Config'den seçenekler çekiliyor
 * - ✅ AI metadata JSON formatında
 */
class GeminiJsonBasedArsaFieldDependencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎯 Gemini JSON-Based Arsa Field Dependency Seeder başlatılıyor...');
        $this->command->info('📋 Source: docs/ai/GEMINI_COMPLETE_SYSTEM_DATA.json v2.0.0');
        $this->command->info('✅ Context7 Compliance: status, display_order');
        $this->command->newLine();

        // Tablo kontrolü
        if (!Schema::hasTable('kategori_yayin_tipi_field_dependencies')) {
            $this->command->error('❌ kategori_yayin_tipi_field_dependencies tablosu bulunamadı!');

            return;
        }

        // Context7: Status kolonu kontrolü
        $hasStatusColumn = Schema::hasColumn('kategori_yayin_tipi_field_dependencies', 'status');
        $hasAiCalculationColumn = Schema::hasColumn('kategori_yayin_tipi_field_dependencies', 'ai_calculation');

        if (!$hasStatusColumn) {
            $this->command->error('❌ status kolonu bulunamadı! Context7 uyumluluğu için status kolonu gerekli.');

            return;
        }

        // JSON dosyasını yükle
        $jsonPath = base_path('docs/ai/GEMINI_COMPLETE_SYSTEM_DATA.json');

        if (!file_exists($jsonPath)) {
            $this->command->error('❌ JSON dosyası bulunamadı: ' . $jsonPath);

            return;
        }

        $jsonContent = file_get_contents($jsonPath);
        $data = json_decode($jsonContent, true);

        // JSON yapısını kontrol et: field_dependencies.arsa.Satılık
        $arsaFields = null;

        if (isset($data['field_dependencies']['arsa']['Satılık'])) {
            // Yeni format: field_dependencies.arsa.Satılık
            $arsaFields = $data['field_dependencies']['arsa']['Satılık'];
        } elseif (isset($data['field_dependencies']['matrix']['arsa']['Satılık'])) {
            // Alternatif format: field_dependencies.matrix.arsa.Satılık
            $arsaFields = $data['field_dependencies']['matrix']['arsa']['Satılık'];
        }

        if (!$arsaFields || !is_array($arsaFields)) {
            $this->command->error('❌ JSON dosyasında arsa.Satılık field dependencies bulunamadı.');
            $this->command->warn('💡 Beklenen yapı: field_dependencies.arsa.Satılık veya field_dependencies.matrix.arsa.Satılık');

            return;
        }

        $this->command->info('✅ JSON dosyası başarıyla yüklendi.');
        $this->command->info("📊 Arsa × Satılık field sayısı: " . count($arsaFields));
        $this->command->newLine();

        // Arsa × Satılık field'larını seed et
        $this->seedArsaSatilik($arsaFields);

        $this->command->newLine();
        $this->command->info('🎉 Gemini JSON-Based Arsa Field Dependency Seeder tamamlandı!');
    }

    /**
     * Arsa × Satılık field'larını seed et
     */
    private function seedArsaSatilik(array $fields): void
    {
        $this->command->info('🏗️ Arsa × Satılık field dependencies işleniyor...');

        // Mevcut Arsa × Satılık kayıtlarını temizle (opsiyonel)
        // DB::table('kategori_yayin_tipi_field_dependencies')
        //     ->where('kategori_slug', 'arsa')
        //     ->where('yayin_tipi', 'Satılık')
        //     ->delete();

        $insertedCount = 0;

        foreach ($fields as $field) {
            // Config'den imar_statusu seçeneklerini çek (eğer select field ise)
            $fieldOptions = $field['field_options'] ?? null;

            if ($field['field_slug'] === 'imar_statusu' && !$fieldOptions) {
                $imarStatusuOptions = config('yali_options.imar_statusu', []);
                if (!empty($imarStatusuOptions)) {
                    // Config formatını JSON formatına çevir
                    $fieldOptions = [];
                    foreach ($imarStatusuOptions as $key => $value) {
                        if (is_array($value) && isset($value['label'])) {
                            $fieldOptions[$value['label']] = $value['label'];
                        } else {
                            $fieldOptions[$key] = $key;
                        }
                    }
                    $fieldOptions = json_encode($fieldOptions);
                }
            } elseif (is_array($fieldOptions)) {
                // Array ise JSON'a çevir
                $fieldOptions = json_encode($fieldOptions);
            } elseif ($fieldOptions !== null) {
                // String veya başka bir format ise olduğu gibi bırak
                $fieldOptions = is_string($fieldOptions) ? $fieldOptions : json_encode($fieldOptions);
            }

            // AI metadata hazırla (JSON formatında)
            $aiMetadata = $this->buildAiMetadata($field);

            // Field category'yi düzelt (JSON'da fiyat, ama biz fiyatlandirma kullanıyoruz)
            $fieldCategory = $field['field_category'] ?? 'arsa';
            if ($fieldCategory === 'fiyat') {
                $fieldCategory = 'fiyatlandirma'; // Context7: fiyat → fiyatlandirma
            }

            // Field'ı oluştur veya güncelle
            $fieldData = [
                'kategori_slug' => 'arsa',
                'yayin_tipi' => 'Satılık',
                'field_slug' => $field['field_slug'],
                'field_name' => $field['field_name'],
                'field_type' => $field['field_type'],
                'field_category' => $fieldCategory,
                'field_options' => $fieldOptions,
                'field_unit' => $field['field_unit'] ?? null,
                'field_icon' => $field['icon'] ?? $field['field_icon'] ?? null,
                'required' => $field['required'] ?? false,
                'searchable' => $field['searchable'] ?? false,
                'show_in_card' => $field['show_in_card'] ?? false,
                'display_order' => $field['display_order'] ?? 0, // Context7: display_order
                'ai_suggestion' => $field['ai_suggestion'] ?? false,
                'ai_auto_fill' => isset($field['ai_source']) || isset($field['ai_calculation']),
                'ai_calculation' => isset($field['ai_calculation']) && ($field['ai_calculation'] === 'auto_calculate'),
                'ai_prompt_key' => $this->buildAiPromptKey($field, $aiMetadata), // AI metadata'yı buraya ekle
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Context7: status kolonu ekle
            // Migration'da status VARCHAR('Aktif'/'Pasif') veya boolean olabilir
            $statusColumnInfo = DB::selectOne(
                "SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME = 'kategori_yayin_tipi_field_dependencies'
                 AND COLUMN_NAME = 'status'"
            );

            if ($statusColumnInfo && $statusColumnInfo->DATA_TYPE === 'varchar') {
                $fieldData['status'] = 'Aktif'; // VARCHAR ise string
            } else {
                $fieldData['status'] = true; // TINYINT/boolean ise boolean
            }

            // ai_calculation kolonu yoksa çıkar
            if (!Schema::hasColumn('kategori_yayin_tipi_field_dependencies', 'ai_calculation')) {
                unset($fieldData['ai_calculation']);
            }

            // Update or create
            DB::table('kategori_yayin_tipi_field_dependencies')->updateOrInsert(
                [
                    'kategori_slug' => 'arsa',
                    'yayin_tipi' => 'Satılık',
                    'field_slug' => $field['field_slug'],
                ],
                $fieldData
            );

            $insertedCount++;
            $this->command->info("  ✅ Field: {$field['field_name']} ({$field['field_slug']})");
        }

        $this->command->info("📊 Toplam {$insertedCount} field eklendi/güncellendi.");
    }

    /**
     * AI metadata JSON oluştur
     *
     * AI özelliklerini JSON formatında meta_data olarak saklar
     * Frontend'de "Sihirli Değnek" butonunu tetikleyecek
     */
    private function buildAiMetadata(array $field): array
    {
        $metadata = [];

        if (isset($field['ai_source'])) {
            $metadata['ai_source'] = $field['ai_source']; // Örn: "TKGM", "maps", "market_analysis"
        }

        if (isset($field['ai_calculation'])) {
            $metadata['ai_calculation'] = $field['ai_calculation']; // Örn: "auto_calculate", "based_on_m2"
            if (isset($field['calculation_formula'])) {
                $metadata['calculation_formula'] = $field['calculation_formula'];
            }
        }

        if (isset($field['ai_suggestion']) && $field['ai_suggestion']) {
            $metadata['ai_suggestion'] = true;
        }

        return $metadata;
    }

    /**
     * AI prompt key oluştur
     *
     * AI metadata'yı JSON formatında ai_prompt_key'e ekler
     * Alternatif: Eğer meta_data kolonu varsa oraya koy
     */
    private function buildAiPromptKey(array $field, array $aiMetadata): ?string
    {
        if (empty($aiMetadata)) {
            return null;
        }

        // Prompt key format: "arsa-ada_no-suggest" veya JSON metadata ile birlikte
        $baseKey = "{$field['field_slug']}-suggest";

        // JSON metadata'yı base64 encode ederek ekleyebiliriz
        // Veya sadece metadata'yı JSON string olarak saklayabiliriz
        if (!empty($aiMetadata)) {
            // Metadata'yı JSON string olarak prompt key'e ekle
            // Frontend'de parse edilecek
            return json_encode([
                'prompt_key' => $baseKey,
                'metadata' => $aiMetadata,
            ]);
        }

        return $baseKey;
    }
}
