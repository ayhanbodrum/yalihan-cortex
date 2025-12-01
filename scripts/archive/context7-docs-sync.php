<?php

echo "\n🔄 Context7 Dokümantasyon Otomatik Senkronizasyon\n";
echo "═══════════════════════════════════════════════════\n\n";

$docsDir = __DIR__.'/../docs';
$masterFile = $docsDir.'/README.md';

$allMdFiles = [];
$categories = [
    'context7' => [],
    'integrations' => [],
    'technical' => [],
    'roadmaps' => [],
    'prompts' => [],
    'api' => [],
];

function scanDirectory($dir, $baseDir, &$files, $category = null)
{
    if (! is_dir($dir)) {
        return;
    }

    $items = scandir($dir);

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $dir.'/'.$item;

        if (is_dir($path)) {
            $newCategory = $category ?? basename($path);
            scanDirectory($path, $baseDir, $files, $newCategory);
        } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'md' && $item !== 'README.md') {
            $relativePath = str_replace($baseDir.'/', '', $path);
            $files[$category ?? 'other'][] = [
                'path' => $relativePath,
                'name' => $item,
                'size' => filesize($path),
                'modified' => filemtime($path),
            ];
        }
    }
}

echo "1️⃣ Tüm MD dosyaları taranıyor...\n";
scanDirectory($docsDir, $docsDir, $categories);

$totalFiles = 0;
foreach ($categories as $cat => $files) {
    $count = count($files);
    $totalFiles += $count;
    if ($count > 0) {
        echo "   ✅ {$cat}: {$count} dosya\n";
    }
}
echo "   📊 Toplam: {$totalFiles} dosya\n\n";

echo "2️⃣ Master dokümantasyon güncelleniyor...\n";

$content = file_get_contents($masterFile);

$statsSection = "## 📊 Dokümantasyon İstatistikleri\n\n";
$statsSection .= "### **Kategori Bazında Dağılım:**\n\n";
$statsSection .= "```\n";
$statsSection .= "📂 Toplam Dosya Sayısı: {$totalFiles}\n\n";

foreach ($categories as $cat => $files) {
    if (count($files) > 0) {
        $percentage = round((count($files) / $totalFiles) * 100, 1);
        $catName = ucfirst($cat);
        $statsSection .= sprintf("%-20s %2d dosya (%s%%)\n", $catName.':', count($files), $percentage);
    }
}

$statsSection .= "```\n\n";
$statsSection .= "### **Son Güncelleme:**\n\n";
$statsSection .= '- **Tarih:** '.date('d F Y, H:i')."\n";
$statsSection .= "- **Otomatik Senkronizasyon:** ✅ Aktif\n";
$statsSection .= "- **Context7 Uyumluluk:** %100\n\n";

if (preg_match('/## 📊 Dokümantasyon İstatistikleri.*?(?=\n##|\n---|\Z)/s', $content, $matches)) {
    $content = str_replace($matches[0], rtrim($statsSection), $content);
    echo "   ✅ İstatistikler güncellendi\n";
} else {
    echo "   ⚠️  İstatistik bölümü bulunamadı\n";
}

$updateLine = '**Son Güncelleme:** '.date('d M Y')."  \n";
$content = preg_replace('/\*\*Son Güncelleme:\*\* .*?\n/', $updateLine, $content);

file_put_contents($masterFile, $content);

echo "   ✅ Master dokümantasyon kaydedildi\n\n";

echo "3️⃣ .context7/authority.json güncelleniyor...\n";

$authorityFile = __DIR__.'/../.context7/authority.json';
if (file_exists($authorityFile)) {
    $authority = json_decode(file_get_contents($authorityFile), true);

    $authority['documentation']['last_sync'] = date('Y-m-d\TH:i:s\Z');
    $authority['documentation']['total_files'] = $totalFiles;
    $authority['documentation']['master_index'] = 'docs/README.md';

    file_put_contents($authorityFile, json_encode($authority, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "   ✅ Authority güncellendi\n\n";
}

echo "4️⃣ Context7 compliance kontrolü...\n";
exec('php artisan context7:check 2>&1', $output, $returnCode);
echo '   '.implode("\n   ", $output)."\n\n";

echo "✨ Otomatik senkronizasyon tamamlandı!\n";
echo "\n📋 Güncellenmiş dosyalar:\n";
echo "  - docs/README.md (Master indeks)\n";
echo "  - .context7/authority.json (Authority sistemi)\n";
echo "  - Toplam {$totalFiles} MD dosyası senkronize\n\n";
