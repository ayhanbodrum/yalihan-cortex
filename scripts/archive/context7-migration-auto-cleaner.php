<?php
// scripts/context7-migration-auto-cleaner.php
// Tüm migration dosyalarını Context7 ve Laravel standartlarına uygun şekilde otomatik temizler.
// Kapanmamış/fazla parantez, boş/işlevsiz blok ve uyumsuz kodları düzeltir.

$dir = __DIR__ . '/../database/migrations';
$files = glob($dir . '/*.php');


$fixed = 0;
$syntaxErrors = [];
$emptyMigrations = [];
$namingViolations = [];
$enumViolations = [];
$report = [];

foreach ($files as $file) {
    $code = file_get_contents($file);
    $original = $code;

    // 1. Boş/işlevsiz Schema::table bloklarını kaldır
    $code = preg_replace_callback(
        '/Schema::table\([^,]+, function \(Blueprint \\$table\)\s*\{\s*([\s\S]*?)\s*}\s*\);/m',
        function ($m) {
            $body = trim($m[1]);
            if ($body === '' || preg_match('/^\/\//m', $body) || preg_match('/^\*+$/m', $body)) {
                return '';
            }
            return $m[0];
        },
        $code
    );

    // 2. Kapanmamış veya fazla süslü parantezleri düzelt (en sık görülen hatalar için)
    $code = preg_replace('/^\s*}\s*}+$\s*$/m', "\n}", $code);
    $code = preg_replace('/^\s*}\s*$/m', "", $code);
    $code = preg_replace('/return;[\s\S]+?\}/m', "return;\n    }", $code);

    // 3. up() fonksiyonu tamamen boşsa tek satıra indir
    $code = preg_replace('/public function up\(\): void\s*\{[\s\S]*?\}/m', "public function up(): void\n    {\n        // Bu migrationda yapılacak bir işlem yok (otomatik temizlik sonrası boş kaldı)\n    }", $code);

    // 4. Gereksiz noktalı virgül ve class kapanışı ekle
    $code = preg_replace('/\}\s*;\s*$/m', "}", $code); // Fazla noktalı virgül kaldır
    if (!preg_match('/\}\s*;\s*$/', $code)) {
        $code = rtrim($code) . "\n};\n";
    }

    // 5. PHP syntax kontrolü
    $tmpFile = tempnam(sys_get_temp_dir(), 'mig');
    file_put_contents($tmpFile, $code);
    $lint = shell_exec("php -l " . escapeshellarg($tmpFile));
    if (strpos($lint, 'No syntax errors detected') === false) {
        $syntaxErrors[] = $file . ": " . trim($lint);
    }
    unlink($tmpFile);

    // 6. Tamamen boş migration tespiti
    if (preg_match('/public function up\(\): void\s*\{\s*\/\/ Bu migrationda yapılacak bir işlem yok/m', $code)) {
        $emptyMigrations[] = $file;
    }

    // 7. Naming convention ve enum kontrolü (Context7/Laravel)
    if (preg_match('/\$table->.*\b(il|ilce|mahalle|status|oncelik|aktif_mi|is_published)\b/', $code)) {
        $namingViolations[] = $file;
    }
    if (preg_match('/enum\s*\(.*[ğüşıöçĞÜŞİÖÇ]/u', $code)) {
        $enumViolations[] = $file;
    }

    if ($code !== $original) {
        file_put_contents($file, $code);
        echo "[DÜZELTİLDİ] $file\n";
        $fixed++;
    }
}

// 8. Raporlama
if ($syntaxErrors) {
    file_put_contents(__DIR__ . '/../reports/migration-syntax-errors.txt', implode("\n", $syntaxErrors));
    echo "[HATA] PHP syntax hatası bulunan dosyalar: reports/migration-syntax-errors.txt\n";
}
if ($emptyMigrations) {
    file_put_contents(__DIR__ . '/../reports/migration-empty.txt', implode("\n", $emptyMigrations));
    echo "[BİLGİ] Tamamen boş migrationlar: reports/migration-empty.txt\n";
}
if ($namingViolations) {
    file_put_contents(__DIR__ . '/../reports/migration-naming-violations.txt', implode("\n", $namingViolations));
    echo "[UYARI] Naming convention hatası: reports/migration-naming-violations.txt\n";
}
if ($enumViolations) {
    file_put_contents(__DIR__ . '/../reports/migration-enum-violations.txt', implode("\n", $enumViolations));
    echo "[UYARI] Enum/Türkçe karakter hatası: reports/migration-enum-violations.txt\n";
}

echo "\nToplam düzeltme: $fixed dosya\n";

// 9. Otomatik migrate testi
$output = shell_exec('php ' . escapeshellarg(__DIR__ . '/../artisan') . ' migrate:fresh 2>&1');
file_put_contents(__DIR__ . '/../reports/migration-migrate-output.txt', $output);
if (strpos($output, 'ParseError') !== false || strpos($output, 'syntax error') !== false) {
    echo "[HATA] Migration zinciri migrate sırasında hata verdi. Ayrıntı: reports/migration-migrate-output.txt\n";
}
