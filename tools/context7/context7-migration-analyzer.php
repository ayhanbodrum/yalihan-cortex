<?php

/**
 * Context7 Migration Analyzer
 * Detects Context7 violations in migration files
 */
class Context7MigrationAnalyzer
{
    private array $violations = [];

    private array $turkishToEnglish = [
        // Field names
        'ad' => 'name',
        'soyad' => 'last_name',
        'baslik' => 'title',
        'aciklama' => 'description',
        'kategori_id' => 'category_id',
        'kullanici_id' => 'user_id',
        'status' => 'status',
        'aktif' => 'active',
        'il' => 'city',
        'il_id' => 'city_id',
        'il' => 'province',
        'il_id' => 'province_id',
        'ilce' => 'district',
        'ilce_id' => 'district_id',
        'mahalle' => 'neighborhood',
        'mahalle_id' => 'neighborhood_id',
        'adres' => 'address',
        'telefon' => 'phone',
        'notlar' => 'notes',
        'kaynak' => 'source',
        'sira' => 'sort_order',
        'tip' => 'type',
        'kanal' => 'channel',
        'mesaj' => 'message',
        'okundu' => 'is_read',
        'gonderim_tarihi' => 'sent_at',
        'okunma_tarihi' => 'read_at',
        'baslangic' => 'start_time',
        'bitis' => 'end_time',
        'konum' => 'location',
        'hatirlatma_gonder' => 'send_reminder',
        'hatirlatma_dakika' => 'reminder_minutes',
        'musteri_id' => 'customer_id',
        'ilan_id' => 'property_listing_id',
        'danisman_id' => 'advisor_id',
        'kisi_id' => 'person_id',
        'takip_tipi' => 'tracking_type',
        'son_takip_tarihi' => 'last_follow_up_date',
        'sonraki_takip_tarihi' => 'next_follow_up_date',
        'oncelik' => 'priority',
        'gelir_duzeyi' => 'income_level',
        'medeni_status' => 'marital_status',
        'dogum_tarihi' => 'birth_date',
        'tc_kimlik' => 'national_id',
        'meslek' => 'occupation',

        // Enum values
        'Aktif' => 'active',
        'Pasif' => 'inactive',
        'Potansiyel' => 'potential',
        'Müşteri' => 'customer',
        'Planlandı' => 'planned',
        'Tamamlandı' => 'completed',
        'İptal Edildi' => 'cancelled',
        'Ertelendi' => 'postponed',
        'Kayıp' => 'lost',
        'Düşük' => 'low',
        'Normal' => 'normal',
        'Yüksek' => 'high',
        'Acil' => 'urgent',
    ];

    public function analyzeMigrationsDirectory(string $directory): array
    {
        $files = glob($directory.'/*.php');

        foreach ($files as $file) {
            $this->analyzeFile($file);
        }

        return $this->violations;
    }

    private function analyzeFile(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $filename = basename($filePath);

        // Check for Turkish field names
        foreach ($this->turkishToEnglish as $turkish => $english) {
            // Skip if it's a comment suggesting the correct translation
            if (strpos($content, "// Context7: $turkish → $english") !== false) {
                continue;
            }

            // Check for field names in table creation
            if (preg_match("/\\\$table->[^(]*\(['\"]{$turkish}['\"]/", $content)) {
                $this->violations[] = [
                    'file' => $filename,
                    'type' => 'turkish_field_name',
                    'violation' => $turkish,
                    'suggestion' => $english,
                    'line' => $this->getLineNumber($content, $turkish),
                ];
            }

            // Check for Turkish enum values
            if (preg_match("/\[.*['\"]{$turkish}['\"]/", $content)) {
                $this->violations[] = [
                    'file' => $filename,
                    'type' => 'turkish_enum_value',
                    'violation' => $turkish,
                    'suggestion' => $english,
                    'line' => $this->getLineNumber($content, $turkish),
                ];
            }

            // Check for foreign key references
            if (preg_match("/->constrained\(['\"][^'\"]*['\"].*{$turkish}/", $content)) {
                $this->violations[] = [
                    'file' => $filename,
                    'type' => 'turkish_reference',
                    'violation' => $turkish,
                    'suggestion' => $english,
                    'line' => $this->getLineNumber($content, $turkish),
                ];
            }
        }
    }

    private function getLineNumber(string $content, string $search): int
    {
        $lines = explode("\n", $content);
        foreach ($lines as $index => $line) {
            if (strpos($line, $search) !== false) {
                return $index + 1;
            }
        }

        return 0;
    }

    public function generateReport(): string
    {
        $report = "# Context7 Migration Analysis Report\n\n";
        $report .= 'Generated: '.date('Y-m-d H:i:s')."\n\n";

        if (empty($this->violations)) {
            $report .= "✅ **No Context7 violations found!**\n\n";

            return $report;
        }

        $report .= '❌ **Found '.count($this->violations)." Context7 violations:**\n\n";

        $groupedViolations = [];
        foreach ($this->violations as $violation) {
            $key = $violation['file'];
            if (! isset($groupedViolations[$key])) {
                $groupedViolations[$key] = [];
            }
            $groupedViolations[$key][] = $violation;
        }

        foreach ($groupedViolations as $file => $violations) {
            $report .= "## 📄 `$file`\n\n";

            foreach ($violations as $violation) {
                $report .= "- **Line {$violation['line']}**: ";
                $report .= ucfirst(str_replace('_', ' ', $violation['type'])).' ';
                $report .= "`{$violation['violation']}` → should be `{$violation['suggestion']}`\n";
            }
            $report .= "\n";
        }

        return $report;
    }
}

// Run analysis
$analyzer = new Context7MigrationAnalyzer;
$violations = $analyzer->analyzeMigrationsDirectory('database/migrations');
$report = $analyzer->generateReport();

echo $report;

// Save report to file
file_put_contents('context7-migration-analysis-report.md', $report);
echo "\n📋 Report saved to: context7-migration-analysis-report.md\n";
