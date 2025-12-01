<?php

// scripts/context7-domain-analyzer.php
// Kategori, alt kategori, yayın tipi ve özellik ilişkilerini analiz eden, çakışma/tutarsızlık tespiti ve öneri/otomatik düzeltme sunan modül.

$root = realpath(__DIR__.'/..');

// 1. Kategori, alt kategori, yayın tipi ve özellik kaynaklarını topla (örnek: config, migration, model, seed, blade)
$categorySources = [
    'app/Models',
    'app/Modules',
    'database/seeders',
    'resources/views',
    'config',
];

$categories = [];
$subcategories = [];
$types = [];
$features = [];
$problems = [];

function extractDomainInfo($file, &$categories, &$subcategories, &$types, &$features)
{
    $lines = @file($file);
    if (! $lines) {
        return;
    }
    foreach ($lines as $ln => $text) {
        // Kategori/alt kategori/özellik/yayın tipi tespiti (örnek regexler, geliştirilebilir)
        if (preg_match('/kategori(_id)?["\'\s=>:]/i', $text)) {
            $categories[] = trim($text);
        }
        if (preg_match('/alt[_-]?kategori(_id)?["\'\s=>:]/i', $text)) {
            $subcategories[] = trim($text);
        }
        if (preg_match('/(yayin|yayın|type|tip|ilan_tipi|ilanTipi)["\'\s=>:]/i', $text)) {
            $types[] = trim($text);
        }
        if (preg_match('/ozellik|özellik|feature|property|attribute["\'\s=>:]/i', $text)) {
            $features[] = trim($text);
        }
    }
}

foreach ($categorySources as $dir) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/'.$dir, FilesystemIterator::SKIP_DOTS));
    foreach ($rii as $file) {
        if (! $file->isFile()) {
            continue;
        }
        $path = $file->getPathname();
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (! in_array($ext, ['php', 'js', 'json', 'md', 'sql']) && substr($path, -10) !== '.blade.php') {
            continue;
        }
        extractDomainInfo($path, $categories, $subcategories, $types, $features);
    }
}

// 2. Basit çakışma/tutarsızlık tespiti
if (empty($categories)) {
    $problems[] = 'Hiç kategori bulunamadı!';
}
if (empty($types)) {
    $problems[] = 'Hiç yayın tipi bulunamadı!';
}
if (empty($features)) {
    $problems[] = 'Hiç özellik bulunamadı!';
}

// Kategori/alt kategori/özellik isimlerinde tekrar veya çakışma
$catCounts = array_count_values(array_map('strtolower', $categories));
foreach ($catCounts as $k => $v) {
    if ($v > 3) {
        $problems[] = "Kategori tekrar/çakışma: $k ($v kez)";
    }
}
$subcatCounts = array_count_values(array_map('strtolower', $subcategories));
foreach ($subcatCounts as $k => $v) {
    if ($v > 3) {
        $problems[] = "Alt kategori tekrar/çakışma: $k ($v kez)";
    }
}
$typeCounts = array_count_values(array_map('strtolower', $types));
foreach ($typeCounts as $k => $v) {
    if ($v > 3) {
        $problems[] = "Yayın tipi tekrar/çakışma: $k ($v kez)";
    }
}
$featureCounts = array_count_values(array_map('strtolower', $features));
foreach ($featureCounts as $k => $v) {
    if ($v > 3) {
        $problems[] = "Özellik tekrar/çakışma: $k ($v kez)";
    }
}

// 3. Raporlama

echo "\n==== DOMAIN ANALİZ RAPORU ====".PHP_EOL;
echo 'Kategori örnekleri: '.count($categories)."\n";
echo 'Alt kategori örnekleri: '.count($subcategories)."\n";
echo 'Yayın tipi örnekleri: '.count($types)."\n";
echo 'Özellik örnekleri: '.count($features)."\n";

if ($problems) {
    echo "\n-- Tespit Edilen Problemler --\n";
    foreach ($problems as $p) {
        echo "- $p\n";
    }
} else {
    echo "\nHiçbir temel problem tespit edilmedi.\n";
}

// 4. (Geliştirilebilir) Otomatik düzeltme/öneri: Burada öneri veya düzeltme kodları eklenebilir.
// ...

exit(0);
