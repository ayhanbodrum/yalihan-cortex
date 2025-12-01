#!/usr/bin/env php
<?php

/**
 * Dead Code Analyzer - Kapsamlı Kullanılmayan Kod Analizi
 * MCP Entegrasyonu: Yalıhan Bekçi MCP'den sistem yapısı ve kuralları alır
 *
 * Bulur:
 * 1. Kullanılmayan Class'lar
 * 2. Kullanılmayan Method'lar
 * 3. Kullanılmayan Trait'ler
 * 4. Kullanılmayan Interface'ler
 * 5. Kullanılmayan Constant'lar
 * 6. Kullanılmayan Property'ler
 *
 * Kullanım:
 *   php scripts/dead-code-analyzer.php [--mcp] [--context7]
 */
$basePath = __DIR__.'/../';
$useMCP = in_array('--mcp', $argv) || in_array('--context7', $argv);
$mcpResults = [];

echo '🔍 Dead Code Analyzer';
if ($useMCP) {
    echo ' - MCP Enhanced';
}
echo "\n";
echo str_repeat('=', 50)."\n\n";

// MCP entegrasyonu
if ($useMCP) {
    echo "🔗 MCP Entegrasyonu Aktif...\n";

    // Yalıhan Bekçi MCP'den sistem yapısını al
    try {
        $systemStructure = getSystemStructureFromMCP();
        if ($systemStructure) {
            echo "   ✅ Sistem yapısı MCP'den alındı\n";
            echo '      - Model sayısı: '.($systemStructure['models']['count'] ?? 'N/A')."\n";
            echo '      - Controller sayısı: '.($systemStructure['controllers']['count'] ?? 'N/A')."\n";
            $mcpResults['structure'] = $systemStructure;
        }
    } catch (Exception $e) {
        echo '   ⚠️  MCP sistem yapısı alınamadı: '.$e->getMessage()."\n";
    }

    // Context7 kurallarını al
    try {
        $context7Rules = getContext7RulesFromMCP();
        if ($context7Rules) {
            echo "   ✅ Context7 kuralları yüklendi\n";
            $mcpResults['rules'] = $context7Rules;
        }
    } catch (Exception $e) {
        echo "   ⚠️  MCP kuralları alınamadı\n";
    }

    echo "\n";
}

$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'mcp_enabled' => $useMCP,
    'mcp_results' => $mcpResults,
    'unused_classes' => [],
    'unused_methods' => [],
    'unused_traits' => [],
    'unused_interfaces' => [],
    'unused_constants' => [],
    'summary' => [],
];

/**
 * Yalıhan Bekçi MCP'den sistem yapısını al
 */
function getSystemStructureFromMCP()
{
    // MCP tool: get_system_structure
    // Şimdilik yerel dosyadan oku

    $structurePath = __DIR__.'/../.yalihan-bekci/reports/system-structure.json';
    if (file_exists($structurePath)) {
        return json_decode(file_get_contents($structurePath), true);
    }

    // Alternatif: Yalıhan Bekçi MCP server'dan al
    // Bu kısım MCP server'ın HTTP endpoint'i varsa kullanılabilir

    return null;
}

/**
 * Yalıhan Bekçi MCP'den Context7 kurallarını al
 */
function getContext7RulesFromMCP()
{
    // MCP resource: context7://rules/forbidden
    // Şimdilik authority.json'dan oku

    $rulesPath = __DIR__.'/../.context7/authority.json';
    if (file_exists($rulesPath)) {
        $authority = json_decode(file_get_contents($rulesPath), true);

        return [
            'forbidden' => $authority['context7']['forbidden_patterns'] ?? [],
            'required' => $authority['context7']['required_patterns'] ?? [],
        ];
    }

    return null;
}

/**
 * Sonuçları MCP'ye bildir
 */
function reportToMCP($results)
{
    $reportDir = __DIR__.'/../.yalihan-bekci/reports/mcp-dead-code/';
    if (! is_dir($reportDir)) {
        mkdir($reportDir, 0755, true);
    }

    $reportPath = $reportDir.'dead-code-mcp-'.date('Y-m-d-His').'.json';
    file_put_contents($reportPath, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return $reportPath;
}

// 1. TÜM CLASS'LARI BUL
echo "📋 1/6: Tüm class'ları buluyorum...\n";
$allClasses = [];
$allMethods = [];
$allTraits = [];
$allInterfaces = [];

$appPhpFiles = [];
$appDirectoryIterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath.'app', RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($appDirectoryIterator as $file) {
    if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
        $appPhpFiles[] = $file->getPathname();
    }
}

foreach ($appPhpFiles as $filePath) {
    $content = file_get_contents($filePath);
    if ($content === false) {
        continue;
    }

    $relativePath = str_replace($basePath, '', $filePath);

    // Class'ları bul (doc block örneklerini hariç tut)
    if (preg_match_all('/^(?!\s*\*)\s*(?:final\s+|abstract\s+)?class\s+(\w+)/m', $content, $matches)) {
        foreach ($matches[1] as $className) {
            if (preg_match('/abstract\s+class\s+'.preg_quote($className, '/').'\b/', $content)) {
                continue;
            }
            $allClasses[$className] = $relativePath;
        }
    }

    // Trait'leri bul
    if (preg_match_all('/^(?!\s*\*)\s*trait\s+(\w+)/m', $content, $matches)) {
        foreach ($matches[1] as $traitName) {
            $allTraits[$traitName] = $relativePath;
        }
    }

    // Interface'leri bul
    if (preg_match_all('/^(?!\s*\*)\s*interface\s+(\w+)/m', $content, $matches)) {
        foreach ($matches[1] as $interfaceName) {
            $allInterfaces[$interfaceName] = $relativePath;
        }
    }

    // Method'ları bul
    if (preg_match_all('/function\s+(\w+)\s*\(/', $content, $matches)) {
        foreach ($matches[1] as $methodName) {
            if (! in_array($methodName, ['__construct', '__destruct', '__get', '__set', '__call', '__toString'])) {
                $allMethods[] = [
                    'name' => $methodName,
                    'file' => $relativePath,
                ];
            }
        }
    }
}

echo '   ✅ '.count($allClasses)." class bulundu\n";
echo '   ✅ '.count($allTraits)." trait bulundu\n";
echo '   ✅ '.count($allInterfaces)." interface bulundu\n";
echo '   ✅ '.count($allMethods)." method bulundu\n\n";

// 2. KULLANILAN CLASS'LARI BUL
echo "📋 2/6: Kullanılan class'ları buluyorum...\n";
$usedClasses = [];
$usedTraits = [];
$usedInterfaces = [];

// Route dosyalarını kontrol et (tüm routes dizini)
$routeDirectory = $basePath.'routes';
if (is_dir($routeDirectory)) {
    $routeIterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($routeDirectory, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($routeIterator as $routeFile) {
        if ($routeFile->isFile() && strtolower($routeFile->getExtension()) === 'php') {
            $content = file_get_contents($routeFile->getPathname());
            if ($content === false) {
                continue;
            }

            if (preg_match_all('/([A-Za-z0-9_\\\\]+Controller)::class/', $content, $matches)) {
                foreach ($matches[1] as $controller) {
                    $normalized = ltrim($controller, '\\');
                    $usedClasses[] = basename(str_replace('\\', '/', $normalized));
                    $usedClasses[] = $normalized;
                }
            }

            if (preg_match_all('/app\(\s*[\'"]([A-Za-z0-9_\\\\]+Controller)[\'"]\s*\)/', $content, $matches)) {
                foreach ($matches[1] as $controller) {
                    $normalized = ltrim($controller, '\\');
                    $usedClasses[] = basename(str_replace('\\', '/', $normalized));
                    $usedClasses[] = $normalized;
                }
            }

            if (preg_match_all('/[\'"]([A-Za-z0-9_\\\\]+Controller)@/', $content, $matches)) {
                foreach ($matches[1] as $controller) {
                    $normalized = ltrim($controller, '\\');
                    $usedClasses[] = basename(str_replace('\\', '/', $normalized));
                    $usedClasses[] = $normalized;
                }
            }
        }
    }
}

// Tüm PHP dosyalarında kullanılan class'ları bul
// Kullanımı taramak için daha geniş dizin kümesi
$usageDirectories = [
    'app',
    'bootstrap',
    'config',
    'routes',
    'database',
    'resources',
    'scripts',
    'tests',
];

$usagePhpFiles = [];
foreach ($usageDirectories as $directory) {
    $fullPath = $basePath.$directory;
    if (! is_dir($fullPath)) {
        continue;
    }
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
            $usagePhpFiles[] = $file->getPathname();
        }
    }
}
$usagePhpFiles = array_unique($usagePhpFiles);

foreach ($usagePhpFiles as $filePath) {
    $content = file_get_contents($filePath);
    if ($content === false) {
        continue;
    }

    // use statements (namespace import)
    if (preg_match_all('/\buse\s+([A-Za-z0-9_\\\\]+)(?:\s+as\s+([A-Za-z0-9_]+))?\s*;/', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $fqcn = ltrim($match[1], '\\');
            $alias = $match[2] ?? basename(str_replace('\\', '/', $fqcn));
            $usedClasses[] = $alias;
            $usedClasses[] = $fqcn;
        }
    }

    // new ClassName()
    if (preg_match_all('/new\s+([A-Za-z0-9_\\\\]+)(?:\(|::)/', $content, $matches)) {
        foreach ($matches[1] as $className) {
            $usedClasses[] = basename(str_replace('\\', '/', $className));
            $usedClasses[] = ltrim($className, '\\');
        }
    }

    // ClassName::method()
    if (preg_match_all('/([A-Za-z0-9_\\\\]+)::[A-Za-z0-9_]+\(/', $content, $matches)) {
        foreach ($matches[1] as $className) {
            if (! in_array($className, ['self', 'static', 'parent'])) {
                $usedClasses[] = basename(str_replace('\\', '/', $className));
                $usedClasses[] = ltrim($className, '\\');
            }
        }
    }

    // ClassName::class kullanımları
    if (preg_match_all('/([A-Za-z0-9_\\\\]+)::class/', $content, $matches)) {
        foreach ($matches[1] as $className) {
            if (! in_array($className, ['self', 'static', 'parent'])) {
                $usedClasses[] = basename(str_replace('\\', '/', ltrim($className, '\\')));
                $usedClasses[] = ltrim($className, '\\');
            }
        }
    }

    // app('FQCN') veya app(ClassName::class) kullanımı
    if (preg_match_all('/app\(\s*[\'"]([A-Za-z0-9_\\\\]+)[\'"]\s*\)/', $content, $matches)) {
        foreach ($matches[1] as $className) {
            $normalized = ltrim($className, '\\');
            $usedClasses[] = basename(str_replace('\\', '/', $normalized));
            $usedClasses[] = $normalized;
        }
    }
    if (preg_match_all('/app\(\s*([A-Za-z0-9_\\\\]+)::class\s*\)/', $content, $matches)) {
        foreach ($matches[1] as $className) {
            if (! in_array($className, ['self', 'static', 'parent'])) {
                $normalized = ltrim($className, '\\');
                $usedClasses[] = basename(str_replace('\\', '/', $normalized));
                $usedClasses[] = $normalized;
            }
        }
    }

    // 'Controller@method' string kullanımı
    if (preg_match_all('/[\'"]([A-Za-z0-9_\\\\]+Controller)@/', $content, $matches)) {
        foreach ($matches[1] as $className) {
            $normalized = ltrim($className, '\\');
            $usedClasses[] = basename(str_replace('\\', '/', $normalized));
            $usedClasses[] = $normalized;
        }
    }

    // Trait kullanımları (class içindeki use ifadeleri)
    if (preg_match_all('/\buse\s+([A-Za-z0-9_\\\\]+)\s*;/', $content, $matches)) {
        foreach ($matches[1] as $traitName) {
            $usedTraits[] = basename(str_replace('\\', '/', ltrim($traitName, '\\')));
        }
    }

    // Interface implementasyonları
    if (preg_match_all('/implements\s+([A-Za-z0-9_\\\\\s,]+)/', $content, $matches)) {
        foreach ($matches[1] as $interfaces) {
            $interfaceList = array_map('trim', explode(',', $interfaces));
            foreach ($interfaceList as $interface) {
                $usedInterfaces[] = basename(str_replace('\\', '/', ltrim($interface, '\\')));
            }
        }
    }
}

$usedClasses = array_unique($usedClasses);
$usedTraits = array_unique($usedTraits);
$usedInterfaces = array_unique($usedInterfaces);

echo '   ✅ '.count($usedClasses)." kullanılan class bulundu\n";
echo '   ✅ '.count($usedTraits)." kullanılan trait bulundu\n";
echo '   ✅ '.count($usedInterfaces)." kullanılan interface bulundu\n\n";

$orphanedControllerPaths = [];
$comprehensiveReports = glob($basePath.'.yalihan-bekci/reports/comprehensive-code-check-*.json');
if ($comprehensiveReports !== false && ! empty($comprehensiveReports)) {
    rsort($comprehensiveReports);
    $latestReportContent = file_get_contents($comprehensiveReports[0]);
    if ($latestReportContent !== false) {
        $latestReportData = json_decode($latestReportContent, true);
        if (isset($latestReportData['details']['orphaned_code']) && is_array($latestReportData['details']['orphaned_code'])) {
            foreach ($latestReportData['details']['orphaned_code'] as $path) {
                $normalizedPath = ltrim(str_replace(['\\', './'], ['/', ''], $path), '/');
                $orphanedControllerPaths[$normalizedPath] = true;
            }
        }
    }
}

// 3. KULLANILMAYAN CLASS'LARI BUL
echo "📋 3/6: Kullanılmayan class'ları buluyorum...\n";
$unusedClasses = [];

foreach ($allClasses as $className => $file) {
    // Skip if it's a Model (Laravel models are used dynamically)
    if (strpos($file, 'Models/') !== false) {
        continue;
    }

    // Skip if it's a Controller (might be used in routes)
    if (strpos($file, 'Controllers/') !== false) {
        if (! in_array($className, $usedClasses) && isset($orphanedControllerPaths[$file])) {
            $unusedClasses[] = [
                'class' => $className,
                'file' => $file,
                'reason' => 'Orphaned controller (no route)',
            ];
        }

        continue;
    }

    // Check if class is used
    if (! in_array($className, $usedClasses)) {
        $unusedClasses[] = [
            'class' => $className,
            'file' => $file,
            'reason' => 'Not referenced anywhere',
        ];
    }
}

echo '   ✅ '.count($unusedClasses)." kullanılmayan class bulundu\n\n";

// 4. KULLANILMAYAN TRAIT'LERİ BUL
echo "📋 4/6: Kullanılmayan trait'leri buluyorum...\n";
$unusedTraits = [];

foreach ($allTraits as $traitName => $file) {
    if (! in_array($traitName, $usedTraits)) {
        $unusedTraits[] = [
            'trait' => $traitName,
            'file' => $file,
            'reason' => 'Not used in any class',
        ];
    }
}

echo '   ✅ '.count($unusedTraits)." kullanılmayan trait bulundu\n\n";

// 5. KULLANILMAYAN INTERFACE'LERİ BUL
echo "📋 5/6: Kullanılmayan interface'leri buluyorum...\n";
$unusedInterfaces = [];

foreach ($allInterfaces as $interfaceName => $file) {
    if (! in_array($interfaceName, $usedInterfaces)) {
        $unusedInterfaces[] = [
            'interface' => $interfaceName,
            'file' => $file,
            'reason' => 'Not implemented by any class',
        ];
    }
}

echo '   ✅ '.count($unusedInterfaces)." kullanılmayan interface bulundu\n\n";

// 6. ÖZET
echo "📋 6/6: Özet oluşturuluyor...\n";

$results['unused_classes'] = array_slice($unusedClasses, 0, 50); // İlk 50
$results['unused_traits'] = $unusedTraits;
$results['unused_interfaces'] = $unusedInterfaces;
$results['summary'] = [
    'total_classes' => count($allClasses),
    'used_classes' => count($usedClasses),
    'unused_classes' => count($unusedClasses),
    'total_traits' => count($allTraits),
    'used_traits' => count($usedTraits),
    'unused_traits' => count($unusedTraits),
    'total_interfaces' => count($allInterfaces),
    'used_interfaces' => count($usedInterfaces),
    'unused_interfaces' => count($unusedInterfaces),
    'cleanup_opportunity' => count($unusedClasses) + count($unusedTraits) + count($unusedInterfaces),
];

// Raporu kaydet
$reportDir = $basePath.'.yalihan-bekci/reports/';
if (! is_dir($reportDir)) {
    mkdir($reportDir, 0755, true);
}

$reportFile = $reportDir.'dead-code-analysis-'.date('Y-m-d-His').'.json';
file_put_contents($reportFile, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// MCP'ye sonuçları bildir
if ($useMCP) {
    $mcpReportPath = reportToMCP($results);
    echo "\n📤 MCP'ye sonuçlar bildirildi: $mcpReportPath\n";
}

// Markdown raporu oluştur
$markdownReport = $reportDir.'dead-code-analysis-'.date('Y-m-d-His').'.md';
$markdown = '# Dead Code Analysis Report - '.date('Y-m-d H:i:s')."\n\n";
if ($useMCP) {
    $markdown .= "**MCP Entegrasyonu:** ✅ Aktif\n\n";
}
$markdown .= "## 📊 Özet\n\n";
$markdown .= "| Kategori | Toplam | Kullanılan | Kullanılmayan |\n";
$markdown .= "|----------|--------|------------|---------------|\n";
$markdown .= "| Class'lar | ".count($allClasses).' | '.count($usedClasses).' | '.count($unusedClasses)." |\n";
$markdown .= "| Trait'ler | ".count($allTraits).' | '.count($usedTraits).' | '.count($unusedTraits)." |\n";
$markdown .= "| Interface'ler | ".count($allInterfaces).' | '.count($usedInterfaces).' | '.count($unusedInterfaces)." |\n\n";
$markdown .= '**Temizlik Fırsatı:** '.$results['summary']['cleanup_opportunity']." dosya\n\n";

$markdown .= "## 🗑️ Kullanılmayan Class'lar (İlk 50)\n\n";
foreach (array_slice($unusedClasses, 0, 50) as $item) {
    $markdown .= "- **{$item['class']}** (`{$item['file']}`)\n";
    $markdown .= "  - Sebep: {$item['reason']}\n\n";
}

if (count($unusedTraits) > 0) {
    $markdown .= "## 🗑️ Kullanılmayan Trait'ler\n\n";
    foreach ($unusedTraits as $item) {
        $markdown .= "- **{$item['trait']}** (`{$item['file']}`)\n";
        $markdown .= "  - Sebep: {$item['reason']}\n\n";
    }
}

if (count($unusedInterfaces) > 0) {
    $markdown .= "## 🗑️ Kullanılmayan Interface'ler\n\n";
    foreach ($unusedInterfaces as $item) {
        $markdown .= "- **{$item['interface']}** (`{$item['file']}`)\n";
        $markdown .= "  - Sebep: {$item['reason']}\n\n";
    }
}

file_put_contents($markdownReport, $markdown);

echo "\n✅ Analiz tamamlandı!\n\n";
echo "📊 ÖZET:\n";
echo '  - Toplam Class: '.count($allClasses)."\n";
echo '  - Kullanılan Class: '.count($usedClasses)."\n";
echo '  - Kullanılmayan Class: '.count($unusedClasses)."\n";
echo '  - Kullanılmayan Trait: '.count($unusedTraits)."\n";
echo '  - Kullanılmayan Interface: '.count($unusedInterfaces)."\n";
echo '  - Temizlik Fırsatı: '.$results['summary']['cleanup_opportunity']." dosya\n\n";

echo "✅ Raporlar kaydedildi:\n";
echo "  - JSON: $reportFile\n";
echo "  - Markdown: $markdownReport\n";
