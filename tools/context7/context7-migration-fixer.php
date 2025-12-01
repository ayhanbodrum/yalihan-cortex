<?php

/**
 * Context7 Migration Auto-Fixer
 * Automatically fixes Context7 violations in migration files
 */
class Context7MigrationFixer
{
    private array $replacements = [
        // Field names
        "'ad'" => "'name'",
        "'soyad'" => "'last_name'",
        "'baslik'" => "'title'",
        "'aciklama'" => "'description'",
        "'kategori_id'" => "'category_id'",
        "'kullanici_id'" => "'user_id'",
        "'status'" => "'status'",
        "'aktif'" => "'is_active'",
        "'il'" => "'city'",
        "'il_id'" => "'city_id'",
        "'il'" => "'province'",
        "'il_id'" => "'province_id'",
        "'ilce'" => "'district'",
        "'ilce_id'" => "'district_id'",
        "'mahalle'" => "'neighborhood'",
        "'mahalle_id'" => "'neighborhood_id'",
        "'adres'" => "'address'",
        "'telefon'" => "'phone'",
        "'notlar'" => "'notes'",
        "'kaynak'" => "'source'",
        "'sira'" => "'sort_order'",
        "'tip'" => "'type'",
        "'kanal'" => "'channel'",
        "'mesaj'" => "'message'",
        "'okundu'" => "'is_read'",
        "'gonderim_tarihi'" => "'sent_at'",
        "'okunma_tarihi'" => "'read_at'",
        "'baslangic'" => "'start_time'",
        "'bitis'" => "'end_time'",
        "'konum'" => "'location'",
        "'hatirlatma_gonder'" => "'send_reminder'",
        "'hatirlatma_dakika'" => "'reminder_minutes'",
        "'musteri_id'" => "'customer_id'",
        "'ilan_id'" => "'property_listing_id'",
        "'danisman_id'" => "'advisor_id'",
        "'kisi_id'" => "'person_id'",
        "'takip_tipi'" => "'tracking_type'",
        "'son_takip_tarihi'" => "'last_follow_up_date'",
        "'sonraki_takip_tarihi'" => "'next_follow_up_date'",
        "'oncelik'" => "'priority'",
        "'gelir_duzeyi'" => "'income_level'",
        "'medeni_status'" => "'marital_status'",
        "'dogum_tarihi'" => "'birth_date'",
        "'tc_kimlik'" => "'national_id'",
        "'meslek'" => "'occupation'",

        // Enum values
        "'Aktif'" => "'active'",
        "'Pasif'" => "'inactive'",
        "'Potansiyel'" => "'potential'",
        "'Müşteri'" => "'customer'",
        "'Planlandı'" => "'planned'",
        "'Tamamlandı'" => "'completed'",
        "'İptal Edildi'" => "'cancelled'",
        "'Ertelendi'" => "'postponed'",
        "'Kayıp'" => "'lost'",
        "'Düşük'" => "'low'",
        "'Normal'" => "'normal'",
        "'Yüksek'" => "'high'",
        "'Acil'" => "'urgent'",

        // Index names in arrays
        "'il_id'" => "'province_id'",
        "'ilce_id'" => "'district_id'",
        "'mahalle_id'" => "'neighborhood_id'",
        "'kategori_id'" => "'category_id'",
        "'ilan_id'" => "'property_listing_id'",
        "'musteri_id'" => "'customer_id'",
        "'danisman_id'" => "'advisor_id'",
        "'kisi_id'" => "'person_id'",

        // Comments - Turkish to English
        '// Kategori' => '// Category',
        '// Özellik' => '// Feature',
        '// Sıralama' => '// Sort order',
        '// Durum' => '// Status',
        '// Aktif/pasif' => '// Active/inactive',
        'Kategori adı' => 'Category name',
        'Kategori açıklaması' => 'Category description',
        'Özellik adı' => 'Feature name',
        'Özellik açıklaması' => 'Feature description',
        'Sıralama' => 'Sort order',
        'Durum (aktif/pasif)' => 'Active status',
        'Müşteri nereden geldi' => 'Customer source',
    ];

    private array $processedFiles = [];

    private array $errors = [];

    public function fixMigrationsDirectory(string $directory): array
    {
        $files = glob($directory.'/*.php');
        $fixed = 0;

        foreach ($files as $file) {
            if ($this->fixFile($file)) {
                $fixed++;
            }
        }

        return [
            'processed' => count($files),
            'fixed' => $fixed,
            'errors' => $this->errors,
        ];
    }

    private function fixFile(string $filePath): bool
    {
        $filename = basename($filePath);

        // Skip already processed files or certain system files
        if (in_array($filename, $this->processedFiles) ||
            strpos($filename, '_create_cache_table') !== false ||
            strpos($filename, '_create_sessions_table') !== false) {
            return false;
        }

        $originalContent = file_get_contents($filePath);
        $content = $originalContent;
        $hasChanges = false;

        // Apply replacements
        foreach ($this->replacements as $search => $replace) {
            $newContent = str_replace($search, $replace, $content);
            if ($newContent !== $content) {
                $content = $newContent;
                $hasChanges = true;
            }
        }

        // Apply special replacements for constrained references
        $constrainedPatterns = [
            "/->constrained\(['\"]ozellik_kategorileri['\"]\)/" => "->constrained('ozellik_kategorileri')", // Keep Turkish table name
            "/->constrained\(['\"]musteriler['\"]\)/" => "->constrained('musteriler')", // Keep Turkish table name
            "/->constrained\(['\"]ilanlar['\"]\)/" => "->constrained('ilanlar')", // Keep Turkish table name
            "/->constrained\(['\"]kisiler['\"]\)/" => "->constrained('kisiler')", // Keep Turkish table name
        ];

        foreach ($constrainedPatterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        if ($hasChanges) {
            // Add Context7 compliance header
            if (! strpos($content, 'Context7 MCP Compliant')) {
                $content = str_replace(
                    '<?php',
                    "<?php\n// Context7 MCP Compliant - English field names and enum values only",
                    $content
                );
            }

            // Backup original file
            $backupPath = $filePath.'.context7-backup';
            if (! file_exists($backupPath)) {
                copy($filePath, $backupPath);
            }

            // Write fixed content
            if (file_put_contents($filePath, $content) === false) {
                $this->errors[] = "Failed to write to: $filename";

                return false;
            }

            $this->processedFiles[] = $filename;
            echo "✅ Fixed: $filename\n";

            return true;
        }

        return false;
    }

    public function generateReport(array $results): string
    {
        $report = "# Context7 Migration Auto-Fix Report\n\n";
        $report .= 'Generated: '.date('Y-m-d H:i:s')."\n\n";

        $report .= "## 📊 Summary\n\n";
        $report .= "- **Files processed**: {$results['processed']}\n";
        $report .= "- **Files fixed**: {$results['fixed']}\n";
        $report .= '- **Errors**: '.count($results['errors'])."\n\n";

        if (! empty($results['errors'])) {
            $report .= "## ❌ Errors\n\n";
            foreach ($results['errors'] as $error) {
                $report .= "- $error\n";
            }
            $report .= "\n";
        }

        $report .= "## 🔧 Applied Fixes\n\n";
        $report .= "### Field Name Conversions:\n";
        $report .= "- `ad` → `name`\n";
        $report .= "- `soyad` → `last_name`\n";
        $report .= "- `baslik` → `title`\n";
        $report .= "- `aciklama` → `description`\n";
        $report .= "- `kategori_id` → `category_id`\n";
        $report .= "- `il_id` → `province_id`\n";
        $report .= "- `ilce_id` → `district_id`\n";
        $report .= "- `mahalle_id` → `neighborhood_id`\n";
        $report .= "- `telefon` → `phone`\n";
        $report .= "- `adres` → `address`\n";
        $report .= "- `notlar` → `notes`\n";
        $report .= "- `sira` → `sort_order`\n";
        $report .= "- And many more...\n\n";

        $report .= "### Enum Value Conversions:\n";
        $report .= "- `'Aktif'` → `'active'`\n";
        $report .= "- `'Pasif'` → `'inactive'`\n";
        $report .= "- `'Potansiyel'` → `'potential'`\n";
        $report .= "- `'Tamamlandı'` → `'completed'`\n";
        $report .= "- `'İptal Edildi'` → `'cancelled'`\n";
        $report .= "- And more...\n\n";

        $report .= "## 🛡️ Backup Information\n\n";
        $report .= "All original files have been backed up with `.context7-backup` extension.\n";
        $report .= "To restore a file: `cp filename.php.context7-backup filename.php`\n\n";

        $report .= "## ✅ Next Steps\n\n";
        $report .= "1. Review the changes in your version control system\n";
        $report .= "2. Run migrations to test the changes\n";
        $report .= "3. Update corresponding models and seeders if necessary\n";
        $report .= "4. Run Context7 validation: `php artisan context7:check`\n";

        return $report;
    }
}

// Run the fixer
echo "🚀 Starting Context7 Migration Auto-Fixer...\n\n";

$fixer = new Context7MigrationFixer;
$results = $fixer->fixMigrationsDirectory('database/migrations');
$report = $fixer->generateReport($results);

echo "\n".$report;

// Save report
file_put_contents('context7-migration-fix-report.md', $report);
echo "\n📋 Report saved to: context7-migration-fix-report.md\n";
