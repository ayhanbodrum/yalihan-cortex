<?php

/**
 * Yalıhan Bekçi Auto-Fix Script
 * 
 * Tüm Admin Controller'ları AdminController'dan extend edecek şekilde günceller
 * Context7: Undefined variable sorunlarını çözmek için
 */

$controllerPath = __DIR__ . '/../app/Http/Controllers/Admin';
$files = glob($controllerPath . '/*.php');

$updated = 0;
$skipped = 0;
$errors = 0;

foreach ($files as $file) {
    $filename = basename($file);
    
    // AdminController.php'yi ve AI subdirectory'deki dosyaları atla
    if ($filename === 'AdminController.php') {
        echo "⏭️  SKIPPED: {$filename} (base controller)\n";
        $skipped++;
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Zaten AdminController'dan extend ediyorsa atla
    if (strpos($content, 'extends AdminController') !== false) {
        echo "⏭️  SKIPPED: {$filename} (already extends AdminController)\n";
        $skipped++;
        continue;
    }
    
    // Controller'dan extend etmeyen dosyaları atla (trait, interface vb.)
    if (strpos($content, 'extends Controller') === false) {
        echo "⏭️  SKIPPED: {$filename} (not a controller)\n";
        $skipped++;
        continue;
    }
    
    // Değiştir: extends Controller → extends AdminController
    $newContent = str_replace('extends Controller', 'extends AdminController', $content);
    
    // use App\Http\Controllers\Controller; satırını kaldır (artık gerek yok)
    $newContent = preg_replace('/use App\\\\Http\\\\Controllers\\\\Controller;\n/', '', $newContent);
    
    // Dosyayı kaydet
    if ($content !== $newContent) {
        file_put_contents($file, $newContent);
        echo "✅ UPDATED: {$filename}\n";
        $updated++;
    } else {
        echo "⚠️  WARNING: {$filename} (no changes)\n";
        $errors++;
    }
}

echo "\n📊 SUMMARY:\n";
echo "✅ Updated: {$updated}\n";
echo "⏭️  Skipped: {$skipped}\n";
echo "⚠️  Warnings: {$errors}\n";
echo "\n🎉 Done! All admin controllers now extend AdminController.\n";

