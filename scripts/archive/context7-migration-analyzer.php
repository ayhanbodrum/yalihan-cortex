<?php

// Context7: Migration dosyalarında PHPDoc açıklama bloklarını ve fazla kapanış parantezlerini otomatik temizle
$dir = __DIR__.'/../database/migrations';
$files = glob($dir.'/*.php');
$fixed = 0;
foreach ($files as $file) {
    $code = file_get_contents($file);
    $orig = $code;
    // PHPDoc açıklama bloklarını kaldır
    $code = preg_replace('/\s*\/\*\*([\s\S]*?)\*\//m', '', $code);
    // Fazla kapanış parantezlerini düzelt
    $code = preg_replace('/\}\s*\}\s*;/', "}\n};", $code);
    // Fazla noktalı virgül kaldır
    $code = preg_replace('/\}\s*;\s*$/m', "}\n", $code);
    // Dosya sonunda class kapanışı yoksa ekle
    if (! preg_match('/\}\s*;\s*$/', $code)) {
        $code = rtrim($code)."\n};\n";
    }
    if ($code !== $orig) {
        file_put_contents($file, $code);
        $fixed++;
    }
}
if ($fixed > 0) {
    echo "[MIGRATION ANALYZER] $fixed migration dosyasında açıklama ve parantez temizliği yapıldı.\n";
} else {
    echo "[MIGRATION ANALYZER] Temizlik gerektiren migration dosyası bulunamadı.\n";
}
