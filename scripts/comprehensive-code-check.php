#!/usr/bin/env php
<?php

/**
 * Kapsamlı Kod Kontrolü Scripti
 * 
 * Context7 Standardı: C7-CODE-CHECK-2025-11-05
 * 
 * Yalıhan Bekçi - Kapsamlı Kod Analizi
 * 
 * Bulur:
 * 1. Lint hataları (Syntax, Type)
 * 2. Dead Code (Kullanılmayan kodlar)
 * 3. Orphaned Code (Yetim kodlar)
 * 4. Incomplete Implementation (TODO/FIXME)
 * 5. Disabled Code (Devre dışı kodlar)
 * 6. Code Duplication (Kod tekrarı)
 * 7. Security Issues (Güvenlik)
 * 8. Performance Issues (N+1, slow queries)
 * 9. Dependency Issues (Bağımlılıklar)
 * 10. Code Coverage (Test kapsamı)
 */

$basePath = __DIR__ . '/../';
$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'summary' => [],
    'details' => []
];

// 1. LINT KONTROLÜ
echo "🔍 1/10: Lint Kontrolü...\n";
$lintErrors = [];
$phpFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath . 'app'),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($phpFiles as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace($basePath, '', $file->getPathname());
        
        // Syntax kontrolü
        $output = [];
        $returnVar = 0;
        exec("php -l " . escapeshellarg($file->getPathname()) . " 2>&1", $output, $returnVar);
        
        if ($returnVar !== 0) {
            $lintErrors[] = [
                'file' => $relativePath,
                'error' => implode("\n", $output)
            ];
        }
    }
}

$results['details']['lint'] = $lintErrors;
$results['summary']['lint'] = count($lintErrors);

// 2. DEAD CODE ANALYSIS
echo "🔍 2/10: Dead Code Analizi...\n";
$deadCode = [];
$allClasses = [];
$allMethods = [];
$calledMethods = [];

// Tüm sınıf ve metodları bul
foreach ($phpFiles as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        
        // Class isimlerini bul
        if (preg_match_all('/class\s+(\w+)/', $content, $matches)) {
            foreach ($matches[1] as $className) {
                $allClasses[] = $className;
            }
        }
        
        // Method çağrılarını bul
        if (preg_match_all('/(\w+)::(\w+)|->(\w+)\(/', $content, $matches)) {
            foreach ($matches[1] as $class) {
                if ($class) $calledMethods[] = $class;
            }
            foreach ($matches[2] as $method) {
                if ($method) $calledMethods[] = $method;
            }
            foreach ($matches[3] as $method) {
                if ($method) $calledMethods[] = $method;
            }
        }
    }
}

$results['summary']['dead_code'] = count($allClasses) - count(array_unique($calledMethods));
$results['details']['dead_code'] = [
    'total_classes' => count($allClasses),
    'called_methods' => count(array_unique($calledMethods)),
    'unused_potential' => count($allClasses) - count(array_unique($calledMethods))
];

// 3. ORPHANED CODE
echo "🔍 3/10: Orphaned Code (Yetim Kod) Analizi...\n";
$orphanedControllers = [];
$routeFiles = [
    'routes/web.php',
    'routes/api.php',
    'routes/admin.php',
];

$allRoutes = [];
foreach ($routeFiles as $routeFile) {
    $filePath = $basePath . $routeFile;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        // Controller sınıflarını bul
        if (preg_match_all('/\[(.*?Controller::class)/', $content, $matches)) {
            foreach ($matches[1] as $controller) {
                $allRoutes[] = trim($controller);
            }
        }
    }
}

// Controller dosyalarını bul
$controllerFiles = glob($basePath . 'app/Http/Controllers/**/*Controller.php');
foreach ($controllerFiles as $controllerFile) {
    $content = file_get_contents($controllerFile);
    if (preg_match('/class\s+(\w+Controller)/', $content, $match)) {
        $controllerName = $match[1];
        $found = false;
        foreach ($allRoutes as $route) {
            if (str_contains($route, $controllerName)) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $orphanedControllers[] = str_replace($basePath, '', $controllerFile);
        }
    }
}

$results['summary']['orphaned_code'] = count($orphanedControllers);
$results['details']['orphaned_code'] = $orphanedControllers;

// 4. INCOMPLETE IMPLEMENTATION
echo "🔍 4/10: Incomplete Implementation (Yarım Kalmış Kod) Analizi...\n";
$incomplete = [
    'todos' => [],
    'empty_methods' => [],
    'stub_methods' => []
];

foreach ($phpFiles as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace($basePath, '', $file->getPathname());
        
        // TODO/FIXME bul
        if (preg_match_all('/\/\/.*(TODO|FIXME|HACK|XXX)/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $incomplete['todos'][] = [
                    'file' => $relativePath,
                    'line' => $line,
                    'text' => trim(substr($match[0], 0, 100))
                ];
            }
        }
        
        // Boş metodlar bul
        if (preg_match_all('/function\s+(\w+)\s*\([^)]*\)\s*\{[\s]*\}/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[1] as $index => $methodName) {
                $line = substr_count(substr($content, 0, $matches[0][$index][1]), "\n") + 1;
                $incomplete['empty_methods'][] = [
                    'file' => $relativePath,
                    'line' => $line,
                    'method' => $methodName
                ];
            }
        }
        
        // Stub metodlar (return null; ile biten)
        if (preg_match_all('/function\s+(\w+)\s*\([^)]*\)\s*\{[\s]*(return null;|return;|throw)/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[1] as $index => $methodName) {
                $line = substr_count(substr($content, 0, $matches[0][$index][1]), "\n") + 1;
                $incomplete['stub_methods'][] = [
                    'file' => $relativePath,
                    'line' => $line,
                    'method' => $methodName
                ];
            }
        }
    }
}

$results['summary']['incomplete'] = [
    'todos' => count($incomplete['todos']),
    'empty_methods' => count($incomplete['empty_methods']),
    'stub_methods' => count($incomplete['stub_methods'])
];
$results['details']['incomplete'] = $incomplete;

// 5. DISABLED CODE
echo "🔍 5/10: Disabled Code (Devre Dışı Kod) Analizi...\n";
$disabledCode = [];

foreach ($routeFiles as $routeFile) {
    $filePath = $basePath . $routeFile;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        if (preg_match_all('/\/\/.*(Route|route).*(disabled|DISABLED|TEMPORARILY)/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $disabledCode[] = [
                    'file' => $routeFile,
                    'line' => $line,
                    'text' => trim(substr($match[0], 0, 100))
                ];
            }
        }
    }
}

// TEMPORARILY DISABLED kodları bul
foreach ($phpFiles as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace($basePath, '', $file->getPathname());
        
        if (preg_match_all('/\/\/.*(TEMPORARILY|DISABLED|disabled)/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $disabledCode[] = [
                    'file' => $relativePath,
                    'line' => $line,
                    'text' => trim(substr($match[0], 0, 100))
                ];
            }
        }
    }
}

$results['summary']['disabled_code'] = count($disabledCode);
$results['details']['disabled_code'] = $disabledCode;

// 6. CODE DUPLICATION
echo "🔍 6/10: Code Duplication (Kod Tekrarı) Analizi...\n";
$duplication = [];

// Basit kod tekrarı kontrolü (50+ karakterlik benzer bloklar)
$methodSignatures = [];
foreach ($phpFiles as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace($basePath, '', $file->getPathname());
        
        // Method signature'larını bul
        if (preg_match_all('/function\s+(\w+)\s*\([^)]*\)\s*\{([^}]{50,})\}/s', $content, $matches)) {
            foreach ($matches[2] as $index => $methodBody) {
                $signature = $matches[1][$index];
                $bodyHash = md5($methodBody);
                
                if (!isset($methodSignatures[$bodyHash])) {
                    $methodSignatures[$bodyHash] = [];
                }
                $methodSignatures[$bodyHash][] = [
                    'file' => $relativePath,
                    'method' => $signature
                ];
            }
        }
    }
}

foreach ($methodSignatures as $hash => $methods) {
    if (count($methods) > 1) {
        $duplication[] = [
            'count' => count($methods),
            'methods' => $methods
        ];
    }
}

$results['summary']['duplication'] = count($duplication);
$results['details']['duplication'] = array_slice($duplication, 0, 20); // İlk 20

// 7. SECURITY ISSUES
echo "🔍 7/10: Security Issues (Güvenlik) Analizi...\n";
$securityIssues = [];

foreach ($phpFiles as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace($basePath, '', $file->getPathname());
        
        // SQL injection riskleri
        if (preg_match_all('/\$_(GET|POST|REQUEST)\[.*?\]/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                // Eğer DB::raw, DB::select ile kullanılıyorsa risk
                $context = substr($content, max(0, $match[1] - 200), 400);
                if (preg_match('/(DB::raw|DB::select|->whereRaw|->orderByRaw)/', $context)) {
                    $securityIssues[] = [
                        'file' => $relativePath,
                        'line' => $line,
                        'type' => 'potential_sql_injection',
                        'text' => trim(substr($match[0], 0, 100))
                    ];
                }
            }
        }
        
        // CSRF koruması eksikliği
        if (preg_match('/Route::(post|put|delete|patch)/', $content) && 
            !preg_match('/middleware.*csrf|VerifyCsrfToken/', $content)) {
            $securityIssues[] = [
                'file' => $relativePath,
                'type' => 'missing_csrf_protection',
                'text' => 'CSRF middleware eksik olabilir'
            ];
        }
    }
}

$results['summary']['security'] = count($securityIssues);
$results['details']['security'] = $securityIssues;

// 8. PERFORMANCE ISSUES
echo "🔍 8/10: Performance Issues (Performans) Analizi...\n";
$performanceIssues = [];

foreach ($phpFiles as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace($basePath, '', $file->getPathname());
        
        // N+1 query potansiyeli (loop içinde query)
        if (preg_match_all('/foreach\s*\([^)]+\)\s*\{[\s\S]{0,500}->(find|where|get|first|create|update|delete)\(/s', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $performanceIssues[] = [
                    'file' => $relativePath,
                    'line' => $line,
                    'type' => 'potential_n_plus_one',
                    'text' => 'Loop içinde database query - N+1 riski'
                ];
            }
        }
        
        // Eager loading eksikliği (with() kullanılmamış)
        if (preg_match('/->(find|where|get|first|paginate)\(/', $content) && 
            !preg_match('/->with\(/', $content)) {
            // Basit kontrol - detaylı analiz için daha gelişmiş araç gerekli
        }
    }
}

$results['summary']['performance'] = count($performanceIssues);
$results['details']['performance'] = array_slice($performanceIssues, 0, 20);

// 9. DEPENDENCY ISSUES
echo "🔍 9/10: Dependency Issues (Bağımlılıklar) Analizi...\n";
$dependencyIssues = [];

// Composer paketlerini kontrol et
$composerLock = $basePath . 'composer.lock';
if (file_exists($composerLock)) {
    $composerData = json_decode(file_get_contents($composerLock), true);
    $installedPackages = [];
    if (isset($composerData['packages'])) {
        foreach ($composerData['packages'] as $package) {
            $installedPackages[] = $package['name'];
        }
    }
    
    // Kullanılmayan paketleri kontrol et (basit kontrol)
    $usedPackages = [];
    foreach ($phpFiles as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            foreach ($installedPackages as $package) {
                $packageName = str_replace('/', '\\', $package);
                if (str_contains($content, $packageName) || str_contains($content, $package)) {
                    $usedPackages[] = $package;
                }
            }
        }
    }
    
    $unusedPackages = array_diff($installedPackages, array_unique($usedPackages));
    $dependencyIssues['unused_packages'] = array_slice($unusedPackages, 0, 10);
}

$results['summary']['dependency'] = count($dependencyIssues['unused_packages'] ?? []);
$results['details']['dependency'] = $dependencyIssues;

// 10. CODE COVERAGE (Test dosyaları kontrolü)
echo "🔍 10/10: Code Coverage (Test Kapsamı) Analizi...\n";
$testFiles = glob($basePath . 'tests/**/*Test.php');
$testClasses = [];
foreach ($testFiles as $testFile) {
    $content = file_get_contents($testFile);
    if (preg_match('/class\s+(\w+Test)/', $content, $match)) {
        $testClasses[] = $match[1];
    }
}

$results['summary']['test_coverage'] = count($testFiles);
$results['details']['test_coverage'] = [
    'total_test_files' => count($testFiles),
    'test_classes' => $testClasses
];

// ÖZET
echo "\n📊 KAPSAMLI KOD KONTROLÜ TAMAMLANDI!\n";
echo "=====================================\n\n";

echo "📋 ÖZET:\n";
echo "  - Lint Hataları: " . $results['summary']['lint'] . "\n";
echo "  - Dead Code: " . ($results['summary']['dead_code'] ?? 0) . "\n";
echo "  - Orphaned Code: " . $results['summary']['orphaned_code'] . "\n";
echo "  - TODO/FIXME: " . $results['summary']['incomplete']['todos'] . "\n";
echo "  - Boş Metodlar: " . $results['summary']['incomplete']['empty_methods'] . "\n";
echo "  - Stub Metodlar: " . $results['summary']['incomplete']['stub_methods'] . "\n";
echo "  - Disabled Code: " . $results['summary']['disabled_code'] . "\n";
echo "  - Code Duplication: " . $results['summary']['duplication'] . "\n";
echo "  - Security Issues: " . $results['summary']['security'] . "\n";
echo "  - Performance Issues: " . $results['summary']['performance'] . "\n";
echo "  - Dependency Issues: " . $results['summary']['dependency'] . "\n";
echo "  - Test Files: " . $results['summary']['test_coverage'] . "\n";

// JSON raporu kaydet
$reportDir = $basePath . '.yalihan-bekci/reports/';
if (!is_dir($reportDir)) {
    mkdir($reportDir, 0755, true);
}

$reportFile = $reportDir . 'comprehensive-code-check-' . date('Y-m-d-His') . '.json';
file_put_contents($reportFile, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\n✅ Detaylı rapor kaydedildi: " . $reportFile . "\n";

// Yalıhan Bekçi'ye öğret
$knowledgeFile = $basePath . '.yalihan-bekci/knowledge/code-check-results-' . date('Y-m-d') . '.json';
file_put_contents($knowledgeFile, json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'results' => $results,
    'recommendations' => generateRecommendations($results)
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✅ Yalıhan Bekçi'ye öğretildi: " . $knowledgeFile . "\n";

function generateRecommendations($results) {
    $recommendations = [];
    
    if ($results['summary']['lint'] > 0) {
        $recommendations[] = [
            'priority' => 'HIGH',
            'action' => 'Lint hatalarını düzelt',
            'count' => $results['summary']['lint']
        ];
    }
    
    if ($results['summary']['orphaned_code'] > 0) {
        $recommendations[] = [
            'priority' => 'MEDIUM',
            'action' => 'Orphaned controller\'ları kaldır veya route\'lara bağla',
            'count' => $results['summary']['orphaned_code']
        ];
    }
    
    if ($results['summary']['incomplete']['todos'] > 50) {
        $recommendations[] = [
            'priority' => 'MEDIUM',
            'action' => 'TODO/FIXME yorumlarını gözden geçir ve tamamla',
            'count' => $results['summary']['incomplete']['todos']
        ];
    }
    
    if ($results['summary']['security'] > 0) {
        $recommendations[] = [
            'priority' => 'CRITICAL',
            'action' => 'Güvenlik sorunlarını hemen düzelt',
            'count' => $results['summary']['security']
        ];
    }
    
    return $recommendations;
}

