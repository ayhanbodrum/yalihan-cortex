<?php

/**
 * Migration Syntax Auto-Fixer
 * Otomatik migration dosyası syntax hatalarını düzeltir
 * Kullanım: php scripts/migration-syntax-auto-fixer.php
 */
class MigrationSyntaxAutoFixer
{
    private $migrationsPath;

    private $fixedFiles = [];

    private $errorFiles = [];

    public function __construct()
    {
        $this->migrationsPath = __DIR__.'/../database/migrations';
    }

    public function run()
    {
        echo "🚀 Migration Syntax Auto-Fixer başlatılıyor...\n";

        $migrationFiles = glob($this->migrationsPath.'/*.php');

        foreach ($migrationFiles as $file) {
            $this->fixMigrationFile($file);
        }

        $this->printSummary();
    }

    private function fixMigrationFile($filePath)
    {
        $filename = basename($filePath);
        echo "🔍 Kontrol ediliyor: $filename\n";

        $content = file_get_contents($filePath);
        $originalContent = $content;

        // 1. Fazla kapama parantezlerini kaldır
        $content = $this->removeExtraBraces($content);

        // 2. Yanlış semicolon kullanımlarını düzelt
        $content = $this->fixSemicolonErrors($content);

        // 3. Eksik fonksiyon kapatmalarını düzelt
        $content = $this->fixMissingFunctionClosures($content);

        // 4. Bozuk class yapılarını düzelt
        $content = $this->fixClassStructure($content);

        // 5. PHPDoc blokları kaldır (Context7 uyumu için)
        $content = $this->removePHPDocBlocks($content);

        // 6. Fazla boşlukları temizle
        $content = $this->cleanExtraSpaces($content);

        if ($content !== $originalContent) {
            if (file_put_contents($filePath, $content)) {
                $this->fixedFiles[] = $filename;
                echo "✅ Düzeltildi: $filename\n";
            } else {
                $this->errorFiles[] = $filename;
                echo "❌ Hata: $filename\n";
            }
        } else {
            echo "✨ Zaten temiz: $filename\n";
        }
    }

    private function removeExtraBraces($content)
    {
        // Fazla kapama parantezlerini kaldır
        $patterns = [
            '/\}\n\s*\}\n\s*\};?\n*$/m' => '};',
            '/\}\n\s*\};\n*$/m' => '};',
            '/\}\n\s*\}\n*$/m' => '}',
            '/\\\n\s*\}\s*\\\n\s*\};?\\\n/m' => '',
            '/\}\\\n\s*\}\\\n\s*\};?\\\n/m' => '};',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        return $content;
    }

    private function fixSemicolonErrors($content)
    {
        // Yanlış semicolon kullanımlarını düzelt
        $patterns = [
            '/\)\s*:\s*void\s*\{\s*([^}]*)\s*\};/m' => ') : void { $1 }',
            '/public function up\(\)\s*:\s*void\s*\{\s*([^}]*)\s*\};/m' => 'public function up(): void { $1 }',
            '/public function down\(\)\s*:\s*void\s*\{\s*([^}]*)\s*\};/m' => 'public function down(): void { $1 }',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        return $content;
    }

    private function fixMissingFunctionClosures($content)
    {
        // Pattern 1: Fix duplicate down() functions (Cannot redeclare)
        $content = preg_replace(
            '/(public function down\(\)\s*:\s*void\s*\{[^}]*\})\s*(public function down\(\)\s*:\s*void\s*\{[^}]*\})/s',
            '$1',
            $content
        );

        // Pattern 2: Fix "unexpected public" - missing newlines
        $content = preg_replace(
            '/(\})\s*(public function)/ms',
            '$1'."\n\n".'    $2',
            $content
        );

        // Pattern 3: Fix "unexpected fully qualified name" - missing opening brace
        $content = preg_replace(
            '/(return new class extends Migration)\s*\\\\n/ms',
            '$1'."\n".'{',
            $content
        );

        // Pattern 4: Fix "unexpected fully qualified name" - missing opening brace for anonymous class
        $content = preg_replace(
            '/(return new class extends Migration)\s*\n(?!\s*\{)/ms',
            '$1'."\n".'{',
            $content
        );

        // Pattern 5: Fix unmatched closing braces
        $content = preg_replace(
            '/(\}\s*)\}(\s*;?\s*)$/ms',
            '$1$2',
            $content
        );

        // Pattern 6: Fix "unexpected token public" after class declaration
        $content = preg_replace(
            '/(class[^{]*\{)\s*(public function)/ms',
            '$1'."\n".'    $2',
            $content
        );

        // Pattern 7: Fix functions with only opening brace and comment
        $content = preg_replace(
            '/^(\s*public function (up|down)\(\)\s*:\s*void\s*\{\s*)\/\/[^\n]*\n(?!\s*\})(.*)$/ms',
            '$1'."\n".'        // Migration content goes here'."\n".'    }',
            $content
        );

        // Pattern 8: Fix unexpected variable
        $content = preg_replace(
            '/(class[^{]*\{)\s*(\$table)/ms',
            '$1'."\n".'    public function up(): void'."\n".'    {'."\n".'        Schema::table(\'tablename\', function (Blueprint $2) {',
            $content
        );

        // Pattern 9: Fix unexpected if/catch/else - missing function wrapper
        $content = preg_replace(
            '/(class[^{]*\{)\s*(if|catch|else|try)/ms',
            '$1'."\n".'    public function up(): void'."\n".'    {'."\n".'        $2',
            $content
        );

        // Eksik fonksiyon kapatmalarını düzelt
        if (! preg_match('/public function down\(\)/', $content) && preg_match('/public function up\(\)/', $content)) {
            // down() fonksiyonu eksikse ekle
            $content = preg_replace(
                '/(public function up\(\)[^}]*\})\s*\};?/s',
                '$1

    public function down(): void
    {
        // Bu migrationda yapılacak bir işlem yok (otomatik temizlik sonrası boş kaldı)
    }
};',
                $content
            );
        }

        // Eksik class kapatmalarını ekle
        if (! preg_match('/\};\s*$/', $content) && preg_match('/return new class extends Migration/', $content)) {
            $content = rtrim($content);
            if (! preg_match('/\}\s*$/', $content)) {
                $content .= "\n    }\n\n    public function down(): void\n    {\n        // Bu migrationda yapılacak bir işlem yok (otomatik temizlik sonrası boş kaldı)\n    }\n};";
            } else {
                $content .= "\n\n    public function down(): void\n    {\n        // Bu migrationda yapılacak bir işlem yok (otomatik temizlik sonrası boş kaldı)\n    }\n};";
            }
        }

        return $content;
    }

    private function fixClassStructure($content)
    {
        // Bozuk class yapılarını düzelt
        $patterns = [
            // Başta fazla kapama parantezi varsa kaldır
            '/^\}\\\n\s*\}\\\n\s*\};\\\n\s*<\?php/m' => '<?php',
            '/^\}\\\n\s*\}\\\n\s*\};\\\n/m' => '',
            // Class başında fazla parantez
            '/^[^<]*(<\?php)/m' => '$1',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        return $content;
    }

    private function removePHPDocBlocks($content)
    {
        // PHPDoc blokları kaldır (Context7 uyumu için)
        $content = preg_replace('/\/\*\*[\s\S]*?\*\/\s*/', '', $content);

        return $content;
    }

    private function cleanExtraSpaces($content)
    {
        // Fazla boşlukları temizle
        $content = preg_replace('/\n\s*\n\s*\n/', "\n\n", $content);
        $content = preg_replace('/\s+$/', '', $content);
        $content = trim($content)."\n";

        return $content;
    }

    private function printSummary()
    {
        echo "\n📊 Özet Rapor:\n";
        echo '✅ Düzeltilen dosyalar: '.count($this->fixedFiles)."\n";
        echo '❌ Hata alan dosyalar: '.count($this->errorFiles)."\n";

        if (! empty($this->fixedFiles)) {
            echo "\n🔧 Düzeltilen dosyalar:\n";
            foreach ($this->fixedFiles as $file) {
                echo "  - $file\n";
            }
        }

        if (! empty($this->errorFiles)) {
            echo "\n⚠️ Hata alan dosyalar:\n";
            foreach ($this->errorFiles as $file) {
                echo "  - $file\n";
            }
        }

        echo "\n🎉 Migration syntax auto-fixer tamamlandı!\n";
    }
}

// Script çalıştır
$fixer = new MigrationSyntaxAutoFixer;
$fixer->run();
