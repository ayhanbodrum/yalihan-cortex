#!/usr/bin/env php
<?php
/**
 * Database Schema Usage Validator
 * Yalıhan Bekçi - 2 Kasım 2025
 * 
 * Query'lerde kullanılan kolon adlarının database'de var olup olmadığını kontrol eder.
 * 
 * NOT: Bu script henüz tam implement edilmedi. Gelecekte:
 * - Database'e bağlanacak
 * - DESCRIBE table_name çalıştıracak
 * - Query'lerdeki kolonları karşılaştıracak
 */

echo "🔍 Database schema validation (BETA)...\n";
echo "⚠️  Bu özellik henüz tam implement edilmedi.\n";
echo "\n";

// Git'te staged PHP dosyalarını al
exec('git diff --cached --name-only --diff-filter=ACM | grep "\.php$"', $files);

if (empty($files)) {
    echo "✅ Kontrol edilecek dosya yok.\n";
    exit(0);
}

echo "📋 Kontrol edilen dosyalar:\n";

$suspiciousPatterns = 0;

foreach ($files as $file) {
    if (!file_exists($file)) {
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Yaygın yanlış kullanımları tespit et
    $suspiciousQueries = [];
    
    // 1. Türkçe kolon adları
    if (preg_match('/->orderBy\([\'"](?:durum|aktif|sehir|ad_soyad)[\'"]/', $content)) {
        $suspiciousQueries[] = "Türkçe kolon adı kullanımı (durum, aktif, sehir, etc.)";
    }
    
    // 2. Context7'de yasaklı kolon adları
    if (preg_match('/->where\([\'"]is_active[\'"]/', $content)) {
        $suspiciousQueries[] = "is_active kullanımı ('enabled' kullanılmalı)";
    }
    
    // 3. ->get(['...']) içinde yaygın hatalar
    if (preg_match('/->get\(\[[^\]]*[\'"]type[\'"][^\]]*\]\)/', $content)) {
        $suspiciousQueries[] = "->get(['type']) kullanımı (etiketler tablosunda yok!)";
    }
    
    if (preg_match('/->get\(\[[^\]]*[\'"]icon[\'"][^\]]*\]\)/', $content)) {
        $suspiciousQueries[] = "->get(['icon']) kullanımı (etiketler tablosunda yok!)";
    }
    
    if (!empty($suspiciousQueries)) {
        echo "\n⚠️  $file:\n";
        foreach ($suspiciousQueries as $query) {
            echo "   - $query\n";
        }
        $suspiciousPatterns++;
    }
}

if ($suspiciousPatterns > 0) {
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "⚠️  WARNING: {$suspiciousPatterns} dosyada şüpheli pattern bulundu!\n";
    echo "\n";
    echo "Öneri:\n";
    echo "1. DESCRIBE table_name; ile gerçek kolonları kontrol et\n";
    echo "2. Model accessor ile column karıştırma\n";
    echo "3. Context7 forbidden patterns (.context7/authority.json)\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n";
    echo "💡 Şu an sadece uyarı veriyor, commit engellenmiyor.\n";
    echo "   Gelecekte: Database'e bağlanıp gerçek schema kontrolü yapacak.\n";
}

echo "\n✅ Schema validation tamamlandı.\n";
exit(0);

