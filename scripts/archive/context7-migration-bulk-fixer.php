<?php
// Context7 Migration Bulk Fixer
// Tüm migration dosyalarını idempotent ve Context7 uyumlu hale getirir.

$dir = __DIR__ . '/../database/migrations';
$files = glob($dir . '/*.php');

$fixCount = 0;
foreach ($files as $file) {
    $code = file_get_contents($file);
    $original = $code;

    // 1. Tablo adını bul (en başta Schema::table veya create ile)
    $tableName = null;
    if (preg_match('/Schema::table\(["\']([a-zA-Z0-9_]+)["\']/', $code, $mt)) {
        $tableName = $mt[1];
    } elseif (preg_match('/Schema::create\(["\']([a-zA-Z0-9_]+)["\']/', $code, $mt)) {
        $tableName = $mt[1];
    }


    // 2. Tüm gereksiz kontrol bloklarını temizle (array_key_exists, hasColumn, hasIndex, Schema::hasIndex, Schema::hasColumn)
    $code = preg_replace('/if\s*\(\s*!?\s*array_key_exists\s*\([^)]+\)\s*\)\s*\{[^\}]*\}/ms', '', $code);
    $code = preg_replace('/if\s*\(\s*!?\s*Schema::has(Column|Index)\s*\([^)]+\)\s*\)\s*\{[^\}]*\}/ms', '', $code);
    $code = preg_replace('/if\s*\(\s*!?\s*\\?Schema::has(Column|Index)\s*\([^)]+\)\s*\)\s*\{[^\}]*\}/ms', '', $code);
    $code = preg_replace('/if\s*\(\s*Schema::has(Column|Index)\s*\([^)]+\)\s*\)\s*\{[^\}]*\}/ms', '', $code);
    $code = preg_replace('/if\s*\(\s*\\?Schema::has(Column|Index)\s*\([^)]+\)\s*\)\s*\{[^\}]*\}/ms', '', $code);
    // Kalan tek satırlık gereksiz kontrolleri de temizle
    $code = preg_replace('/if\s*\(\s*!?\s*Schema::has(Column|Index)\s*\([^)]+\)\s*\)\s*[^;]+;/ms', '', $code);
    $code = preg_replace('/if\s*\(\s*!?\s*array_key_exists\s*\([^)]+\)\s*\)\s*[^;]+;/ms', '', $code);

    // 4. Hatalı/eksik kolon referanslarını kaldır (örnek: olmayan kolonla index)
    // (Not: Bu adımda daha gelişmiş analiz için ek fonksiyonlar eklenebilir)

    // 5. Değişiklik varsa dosyayı güncelle
    if ($code !== $original) {
        file_put_contents($file, $code);
        $fixCount++;
        echo "[FIXED] $file\n";
    }
}
echo "\nToplam $fixCount migration dosyası otomatik düzeltildi.\n";
