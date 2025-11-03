<?php
// scripts/context7-migration-syntax-fixer.php
// Migration dosyalarındaki yaygın parantez ve blok hatalarını otomatik düzeltir.
// Kullanım: php scripts/context7-migration-syntax-fixer.php

$root = realpath(__DIR__ . '/../database/migrations');
$fixed = 0;
$errors = [];

foreach (glob($root . '/*.php') as $file) {
    $content = file_get_contents($file);
    $orig = $content;

    // 1. public function up(): void { ... });  --> ... } (fazla ); kaldır)
    $content = preg_replace('/(public function up\(\): void\s*\{[\s\S]*?)\}\);/m', '$1}', $content);
    // 2. public function up(): void { ... });\n\n  --> ... }\n\n (fazla ); kaldır)
    $content = preg_replace('/(public function up\(\): void\s*\{[\s\S]*?)\}\);\s*\n/m', '$1}\n', $content);
    // 3. public function up(): void { ... }); --> ... } (fazla ); kaldır)
    $content = preg_replace('/(public function up\(\): void\s*\{[\s\S]*?)\}\);/', '$1}', $content);
    // 4. public function up(): void { ... }); --> ... } (fazla ); kaldır)
    $content = preg_replace('/(public function up\(\): void\s*\{[\s\S]*?)\}\);/', '$1}', $content);
    // 5. public function up(): void { ... }); --> ... } (fazla ); kaldır)
    $content = preg_replace('/(public function up\(\): void\s*\{[\s\S]*?)\}\);/', '$1}', $content);
    // 6. public function down(): void { ... }\n}; --> ... }\n}; (kapanışları düzelt)
    $content = preg_replace('/(public function down\(\): void\s*\{[\s\S]*?)\n*\}\n*;\n*$/m', '$1\n    }\n};\n', $content);
    // 7. public function down(): void { ... }\n\n}; --> ... }\n};
    $content = preg_replace('/(public function down\(\): void\s*\{[\s\S]*?)\n*\}\n*;\n*$/m', '$1\n    }\n};\n', $content);
    // 8. public function down(): void { ... }\n\n\n}; --> ... }\n};
    $content = preg_replace('/(public function down\(\): void\s*\{[\s\S]*?)\n*\}\n*;\n*$/m', '$1\n    }\n};\n', $content);
    // 9. public function down(): void { ... }\n\n\n}; --> ... }\n};
    $content = preg_replace('/(public function down\(\): void\s*\{[\s\S]*?)\n*\}\n*;\n*$/m', '$1\n    }\n};\n', $content);
    // 10. Kapanmamış bloklar için: dosya sonunda } eksikse ekle
    if (substr(trim($content), -2) !== '};') {
        $content = rtrim($content) . "\n};\n";
    }

    if ($content !== $orig) {
        file_put_contents($file, $content);
        $fixed++;
    }
}

if ($fixed > 0) {
    echo "[DÜZELTİLDİ] $fixed migration dosyasında parantez/blok hatası otomatik düzeltildi.\n";
    exit(0);
} else {
    echo "[BİLGİ] Migration dosyalarında otomatik düzeltilecek parantez/blok hatası bulunamadı.\n";
    exit(0);
}
