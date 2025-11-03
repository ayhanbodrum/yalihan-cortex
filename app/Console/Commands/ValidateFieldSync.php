<?php

namespace App\Console\Commands;

use App\Services\FieldRegistryService;
use Illuminate\Console\Command;

/**
 * Field Sync Validation Command
 * 
 * Context7 Compliance: %100
 * Yalıhan Bekçi: ✅ Uyumlu
 * 
 * Bu command ilanlar tablosundaki column'ları Field Dependencies ile karşılaştırır
 * ve tutarsızlıkları tespit eder.
 */
class ValidateFieldSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fields:validate
                            {--fix : Otomatik düzeltme önerileri}
                            {--report : Detaylı rapor oluştur}
                            {--category= : Sadece belirli kategoriyi kontrol et}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'İlanlar tablosu ve Field Dependencies arasındaki tutarlılığı kontrol eder';

    /**
     * Field Registry Service
     */
    protected FieldRegistryService $fieldRegistry;

    /**
     * Create a new command instance.
     */
    public function __construct(FieldRegistryService $fieldRegistry)
    {
        parent::__construct();
        $this->fieldRegistry = $fieldRegistry;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Field Sync Validation başlatılıyor...');
        $this->newLine();

        try {
            // Kategori filtresi
            $category = $this->option('category');
            
            // Validation yap
            $result = $this->fieldRegistry->validateSync($category);

            // Sonuçları göster
            $this->displayResults($result);

            // Fix önerileri
            if ($this->option('fix')) {
                $this->showFixSuggestions($result);
            }

            // Detaylı rapor
            if ($this->option('report')) {
                $this->generateReport($result);
            }

            // Exit code
            return $result['has_errors'] ? 1 : 0;

        } catch (\Exception $e) {
            $this->error('❌ Hata: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Sonuçları göster
     */
    protected function displayResults(array $result): void
    {
        // Özet
        $this->info('📊 SONUÇLAR:');
        $this->newLine();

        // Stats
        $this->line("✅ Eşleşen: <fg=green>{$result['stats']['matched']}</>");
        $this->line("⚠️  Eksik (DB'de yok): <fg=yellow>{$result['stats']['missing_in_db']}</>");
        $this->line("⚠️  Fazla (Dependency'de yok): <fg=yellow>{$result['stats']['extra_in_deps']}</>");
        $this->line("❌ Tip Uyumsuzluğu: <fg=red>{$result['stats']['type_mismatch']}</>");
        $this->newLine();

        // Eksik alanlar
        if (!empty($result['missing_in_db'])) {
            $this->warn('⚠️  Field Dependencies\'de var ama ilanlar tablosunda YOK:');
            foreach ($result['missing_in_db'] as $field) {
                $categories = isset($field['categories']) && is_array($field['categories']) 
                    ? implode(', ', $field['categories']) 
                    : 'unknown';
                $this->line("   - {$field['field_slug']} → \"{$field['field_name']}\" ({$categories})");
            }
            $this->newLine();
        }

        // Fazla alanlar
        if (!empty($result['extra_in_deps'])) {
            $this->warn('⚠️  ilanlar tablosunda var ama Field Dependencies\'de YOK:');
            foreach ($result['extra_in_deps'] as $column) {
                $this->line("   - {$column}");
            }
            $this->newLine();
        }

        // Tip uyumsuzlukları
        if (!empty($result['type_mismatches'])) {
            $this->error('❌ Veri tipi uyumsuzlukları:');
            foreach ($result['type_mismatches'] as $mismatch) {
                $this->line("   - {$mismatch['field']}: DB={$mismatch['db_type']}, Dep={$mismatch['dep_type']}");
            }
            $this->newLine();
        }

        // Sonuç mesajı
        if ($result['has_errors']) {
            $this->error('❌ BAŞARISIZ: Tutarsızlıklar tespit edildi!');
        } else {
            $this->info('✅ BAŞARILI: Tüm alanlar senkronize!');
        }
    }

    /**
     * Düzeltme önerileri göster
     */
    protected function showFixSuggestions(array $result): void
    {
        if (!$result['has_errors']) {
            return;
        }

        $this->newLine();
        $this->info('🔧 DÜZELTME ÖNERİLERİ:');
        $this->newLine();

        // Eksik alanlar için migration önerisi
        if (!empty($result['missing_in_db'])) {
            $this->line('<fg=cyan>Migration oluştur:</>');
            $this->line('php artisan make:migration add_missing_fields_to_ilanlar_table');
            $this->newLine();
            
            $this->line('<fg=cyan>Migration içeriği:</>');
            foreach ($result['missing_in_db'] as $field) {
                $columnType = $this->suggestColumnType($field['type'] ?? 'string');
                $this->line("\$table->{$columnType}('{$field['field_name']}')->nullable();");
            }
            $this->newLine();
        }

        // Fazla alanlar için field dependency önerisi
        if (!empty($result['extra_in_deps'])) {
            $this->line('<fg=cyan>Field Dependencies ekle:</>');
            $this->line('Admin Panel → Property Type Manager → Field Dependencies');
            $this->newLine();
        }
    }

    /**
     * Detaylı rapor oluştur
     */
    protected function generateReport(array $result): void
    {
        $filename = 'FIELD_SYNC_REPORT_' . date('Y_m_d_His') . '.md';
        $path = storage_path('logs/' . $filename);

        $content = $this->fieldRegistry->generateMarkdownReport($result);
        
        file_put_contents($path, $content);

        $this->newLine();
        $this->info("📄 Detaylı rapor oluşturuldu: {$filename}");
    }

    /**
     * Column tipi öner
     */
    protected function suggestColumnType(string $type): string
    {
        return match($type) {
            'text' => 'string',
            'number' => 'decimal',
            'integer' => 'integer',
            'boolean' => 'boolean',
            'date' => 'date',
            'datetime' => 'datetime',
            'json' => 'json',
            default => 'string',
        };
    }
}
