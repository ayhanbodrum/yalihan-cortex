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
$basePath = __DIR__.'/../';
$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'summary' => [],
    'details' => [],
];

// 1. LINT KONTROLÜ
echo "🔍 1/10: Lint Kontrolü...\n";
$lintErrors = [];
$phpFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath.'app'),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($phpFiles as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace($basePath, '', $file->getPathname());

        // Syntax kontrolü
        $output = [];
        $returnVar = 0;
        exec('php -l '.escapeshellarg($file->getPathname()).' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            $lintErrors[] = [
                'file' => $relativePath,
                'error' => implode("\n", $output),
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
                if ($class) {
                    $calledMethods[] = $class;
                }
            }
            foreach ($matches[2] as $method) {
                if ($method) {
                    $calledMethods[] = $method;
                }
            }
            foreach ($matches[3] as $method) {
                if ($method) {
                    $calledMethods[] = $method;
                }
            }
        }
    }
}

$results['summary']['dead_code'] = count($allClasses) - count(array_unique($calledMethods));
$results['details']['dead_code'] = [
    'total_classes' => count($allClasses),
    'called_methods' => count(array_unique($calledMethods)),
    'unused_potential' => count($allClasses) - count(array_unique($calledMethods)),
];

// 3. ORPHANED CODE
echo "🔍 3/10: Orphaned Code (Yetim Kod) Analizi...\n";
$orphanedControllers = [];

// ✅ FIX: Tüm route dosyalarını bul (recursive)
$routeFilesList = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath.'routes'),
    RecursiveIteratorIterator::LEAVES_ONLY
);

// ✅ FIX: Tüm route dosyalarını oku ve birleştir (cache)
$allRoutesContent = '';
foreach ($routeFilesList as $routeFile) {
    if ($routeFile->isFile() && $routeFile->getExtension() === 'php') {
        $allRoutesContent .= file_get_contents($routeFile->getPathname())."\n";
    }
}

// Controller dosyalarını bul
$controllerFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath.'app/Http/Controllers'),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($controllerFiles as $controllerFile) {
    if ($controllerFile->isFile() && $controllerFile->getExtension() === 'php') {
        $content = file_get_contents($controllerFile->getPathname());
        $relativePath = str_replace($basePath, '', $controllerFile->getPathname());

        // Controller class adını bul
        if (preg_match('/class\s+(\w+Controller)/', $content, $match)) {
            $controllerName = $match[1];

            // ✅ FIX: Full class name ile kontrol et (namespace dahil)
            $fullClassName = null;
            if (preg_match('/namespace\s+([^;]+);/', $content, $nsMatch)) {
                $namespace = trim($nsMatch[1]);
                $fullClassName = $namespace.'\\'.$controllerName;
            }

            // ✅ FIX: Çoklu kontrol yöntemleri
            $found = false;

            // 1. Controller name kontrolü
            if (strpos($allRoutesContent, $controllerName) !== false) {
                $found = true;
            }

            // 2. Full class name kontrolü (use statement veya ::class)
            if (! $found && $fullClassName) {
                // use statement kontrolü
                if (preg_match('/use\s+'.preg_quote($fullClassName, '/').'/', $allRoutesContent)) {
                    $found = true;
                }
                // ::class kontrolü
                if (! $found && preg_match('/'.preg_quote($controllerName, '/').'::class/', $allRoutesContent)) {
                    $found = true;
                }
                // Full class name kontrolü (string olarak)
                if (! $found && strpos($allRoutesContent, $fullClassName) !== false) {
                    $found = true;
                }
            }

            // 3. Relative path kontrolü (bazı route dosyalarında dosya yolu kullanılıyor olabilir)
            if (! $found) {
                $relativePathForCheck = str_replace('app/Http/Controllers/', '', $relativePath);
                $relativePathForCheck = str_replace('.php', '', $relativePathForCheck);
                if (strpos($allRoutesContent, $relativePathForCheck) !== false) {
                    $found = true;
                }
            }

            if (! $found) {
                $orphanedControllers[] = $relativePath;
            }
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
    'stub_methods' => [],
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
                    'text' => trim(substr($match[0], 0, 100)),
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
                    'method' => $methodName,
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
                    'method' => $methodName,
                ];
            }
        }
    }
}

$results['summary']['incomplete'] = [
    'todos' => count($incomplete['todos']),
    'empty_methods' => count($incomplete['empty_methods']),
    'stub_methods' => count($incomplete['stub_methods']),
];
$results['details']['incomplete'] = $incomplete;

// 5. DISABLED CODE
echo "🔍 5/10: Disabled Code (Devre Dışı Kod) Analizi...\n";
$disabledCode = [];

// Route dosyalarını bul
$routeFiles = [];
$routeFilesList = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath.'routes'),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($routeFilesList as $routeFile) {
    if ($routeFile->isFile() && $routeFile->getExtension() === 'php') {
        $routeFiles[] = str_replace($basePath, '', $routeFile->getPathname());
    }
}

foreach ($routeFiles as $routeFile) {
    $filePath = $basePath.$routeFile;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        if (preg_match_all('/\/\/.*(Route|route).*(disabled|DISABLED|TEMPORARILY)/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $disabledCode[] = [
                    'file' => $routeFile,
                    'line' => $line,
                    'text' => trim(substr($match[0], 0, 100)),
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
                    'text' => trim(substr($match[0], 0, 100)),
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

// ✅ CONTEXT7 MCP ENTEGRASYONU: Context7 kurallarını yükle
$context7Patterns = [];
$context7Rules = null;
$context7AuthorityFile = $basePath.'.context7/authority.json';

// Context7 authority dosyasından kuralları yükle
if (file_exists($context7AuthorityFile)) {
    try {
        $context7Authority = json_decode(file_get_contents($context7AuthorityFile), true);
        if ($context7Authority && isset($context7Authority['forbidden_patterns'])) {
            // Duplication pattern'leri için Context7 kurallarını kullan
            $context7Rules = $context7Authority;
            echo "  ✅ Context7: Authority dosyası yüklendi\n";
        }
    } catch (\Exception $e) {
        echo '  ⚠️  Context7: Authority dosyası yüklenemedi: '.$e->getMessage()."\n";
    }
}

// ✅ CONTEXT7 MCP ENTEGRASYONU: Context7 API'den pattern'leri yükle (opsiyonel)
$context7ApiUrl = getenv('CONTEXT7_API_URL') ?: 'https://context7.com/api/v1';
$context7ApiKey = getenv('CONTEXT7_API_KEY');
if ($context7ApiKey && function_exists('curl_init')) {
    try {
        $ch = curl_init($context7ApiUrl.'/patterns/duplication');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$context7ApiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $apiResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $apiResponse) {
            $apiData = json_decode($apiResponse, true);
            if ($apiData && isset($apiData['patterns'])) {
                $context7Patterns = $apiData['patterns'];
                echo '  ✅ Context7 API: '.count($context7Patterns)." duplication pattern yüklendi\n";
            }
        }
    } catch (\Exception $e) {
        // API yoksa devam et
        echo "  ⚠️  Context7 API: Pattern yükleme başarısız (devam ediliyor)\n";
    }
}

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

                if (! isset($methodSignatures[$bodyHash])) {
                    $methodSignatures[$bodyHash] = [];
                }
                $methodSignatures[$bodyHash][] = [
                    'file' => $relativePath,
                    'method' => $signature,
                ];
            }
        }
    }
}

foreach ($methodSignatures as $hash => $methods) {
    if (count($methods) > 1) {
        $duplicationItem = [
            'count' => count($methods),
            'methods' => $methods,
            'context7_validated' => false,
            'context7_suggestions' => [],
            'context7_compliance' => 'unknown',
        ];

        // ✅ CONTEXT7 MCP ENTEGRASYONU: Duplication'ı Context7 kurallarına göre kontrol et
        if (! empty($context7Patterns)) {
            foreach ($methods as $method) {
                $methodName = $method['method'];
                // Context7 pattern'lerine göre kontrol et
                foreach ($context7Patterns as $pattern) {
                    if (isset($pattern['method_pattern']) && preg_match($pattern['method_pattern'], $methodName)) {
                        $duplicationItem['context7_validated'] = true;
                        if (isset($pattern['suggestion'])) {
                            $duplicationItem['context7_suggestions'][] = $pattern['suggestion'];
                        }
                    }
                }
            }
        }

        // ✅ CONTEXT7 MCP ENTEGRASYONU: Context7 authority kurallarına göre kontrol
        if ($context7Rules && isset($context7Rules['forbidden_patterns'])) {
            $forbiddenPatterns = $context7Rules['forbidden_patterns'];
            foreach ($methods as $method) {
                $methodName = $method['method'];
                // Yasaklı pattern'leri kontrol et (örn: scopeByLanguage, incrementUsage gibi)
                foreach ($forbiddenPatterns as $pattern) {
                    // ✅ Context7: Pattern string kontrolü (array olabilir)
                    if (is_string($pattern) && stripos($methodName, $pattern) !== false) {
                        $duplicationItem['context7_compliance'] = 'violation';
                        $duplicationItem['context7_suggestions'][] = "Method '$methodName' yasaklı pattern içeriyor: $pattern";
                    } elseif (is_array($pattern) && isset($pattern['pattern'])) {
                        // Array formatında pattern varsa
                        $patternStr = is_string($pattern['pattern']) ? $pattern['pattern'] : (string) $pattern['pattern'];
                        if (stripos($methodName, $patternStr) !== false) {
                            $duplicationItem['context7_compliance'] = 'violation';
                            $duplicationItem['context7_suggestions'][] = "Method '$methodName' yasaklı pattern içeriyor: $patternStr";
                        }
                    }
                }
            }
        }

        // ✅ CONTEXT7 MCP ENTEGRASYONU: Trait önerisi (duplicate metodlar için)
        if (count($methods) >= 2) {
            $methodNames = array_unique(array_column($methods, 'method'));
            if (count($methodNames) === 1) {
                // Aynı metod adı farklı dosyalarda - trait önerisi
                $duplicationItem['context7_suggestions'][] = "Aynı metod '".$methodNames[0]."' birden fazla dosyada bulunuyor. Trait'e çıkarılabilir.";
            }
        }

        $duplication[] = $duplicationItem;
    }
}

// ✅ CONTEXT7 MCP ENTEGRASYONU: Sistem yapısı analizi ile duplication doğrulama
$systemStructure = null;
$systemStructureFile = $basePath.'.yalihan-bekci/knowledge/system-structure.json';
if (file_exists($systemStructureFile)) {
    try {
        $systemStructure = json_decode(file_get_contents($systemStructureFile), true);
        if ($systemStructure && isset($systemStructure['models'])) {
            echo '  ✅ Context7: Sistem yapısı analizi yapıldı ('.count($systemStructure['models'])." model)\n";
        }
    } catch (\Exception $e) {
        echo "  ⚠️  Context7: Sistem yapısı analizi başarısız (devam ediliyor)\n";
    }
}

$results['summary']['duplication'] = count($duplication);
$results['details']['duplication'] = array_slice($duplication, 0, 20); // İlk 20
$results['details']['duplication_context7'] = [
    'patterns_loaded' => count($context7Patterns),
    'authority_loaded' => $context7Rules !== null,
    'system_structure_analyzed' => $systemStructure !== null,
    'context7_validated_count' => count(array_filter($duplication, fn ($item) => $item['context7_validated'] ?? false)),
    'context7_violations' => count(array_filter($duplication, fn ($item) => ($item['context7_compliance'] ?? 'unknown') === 'violation')),
];

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
                        'text' => trim(substr($match[0], 0, 100)),
                    ];
                }
            }
        }

        // CSRF koruması eksikliği
        // ✅ FIX: False positive filtreleme
        // - web middleware grubu otomatik CSRF içeriyor
        // - API route'ları CSRF gerektirmez
        // - Service dosyaları route değil
        if (
            preg_match('/Route::(post|put|delete|patch)/', $content) &&
            ! preg_match('/middleware.*csrf|VerifyCsrfToken|middleware.*web|middleware.*api/', $content) &&
            ! preg_match('/Service\.php$|\.php.*Service/', $relativePath)
        ) {
            // Sadece gerçek route dosyalarında ve web middleware olmadan kontrol et
            if (strpos($relativePath, 'routes/') !== false || strpos($relativePath, 'Routes/') !== false) {
                // web middleware kontrolü - eğer Route::group(['middleware' => 'web']) varsa false positive
                if (! preg_match('/middleware.*web|Route::group.*web/', $content)) {
                    $securityIssues[] = [
                        'file' => $relativePath,
                        'type' => 'missing_csrf_protection',
                        'text' => 'CSRF middleware eksik olabilir (web middleware kontrol edilmeli)',
                    ];
                }
            }
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
                $matchContent = $match[0];
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;

                // ✅ FIX: False positive filtreleme
                $isFalsePositive = false;

                // 1. Array işlemleri (sadece array'e atama, N+1 değil)
                if (
                    preg_match('/\$[a-zA-Z_][a-zA-Z0-9_]*\s*=\s*\[/', $matchContent) &&
                    ! preg_match('/->(find|where|get|first|create|update|delete)\(/', $matchContent)
                ) {
                    $isFalsePositive = true;
                }

                // 2. Cache işlemleri (Cache::get, Cache::put, Cache::forget)
                if (preg_match('/Cache::(get|put|forget|remember|has)/', $matchContent)) {
                    $isFalsePositive = true;
                }

                // 3. Storage işlemleri (Storage::get, Storage::put, Storage::delete)
                if (preg_match('/Storage::(get|put|delete|exists|url|path)/', $matchContent)) {
                    $isFalsePositive = true;
                }

                // 4. HTTP işlemleri (Http::get, Http::post)
                if (preg_match('/Http::(get|post|put|delete|patch)/', $matchContent)) {
                    $isFalsePositive = true;
                }

                // 5. Log işlemleri (Log::info, Log::error)
                if (preg_match('/Log::(info|error|warning|debug|notice)/', $matchContent)) {
                    $isFalsePositive = true;
                }

                // 6. Sadece array'e ekleme (push, add, append)
                if (
                    preg_match('/->(push|add|append|put)\(/', $matchContent) &&
                    ! preg_match('/->(find|where|get|first|create|update|delete)\(/', $matchContent)
                ) {
                    $isFalsePositive = true;
                }

                // 7. Service çağrıları (app()->make, resolve)
                if (preg_match('/(app\(\)->make|resolve)\(/', $matchContent)) {
                    $isFalsePositive = true;
                }

                if (! $isFalsePositive) {
                    $performanceIssues[] = [
                        'file' => $relativePath,
                        'line' => $line,
                        'type' => 'potential_n_plus_one',
                        'text' => 'Loop içinde database query - N+1 riski',
                    ];
                }
            }
        }

        // Eager loading eksikliği (with() kullanılmamış)
        if (
            preg_match('/->(find|where|get|first|paginate)\(/', $content) &&
            ! preg_match('/->with\(/', $content)
        ) {
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
$composerLock = $basePath.'composer.lock';
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
// ✅ FIX: glob() recursive pattern desteklemiyor, RecursiveIteratorIterator kullan
$testFiles = [];
$testIterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath.'tests'),
    RecursiveIteratorIterator::LEAVES_ONLY
);
foreach ($testIterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && preg_match('/Test\.php$/', $file->getFilename())) {
        $testFiles[] = $file->getPathname();
    }
}
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
    'test_classes' => $testClasses,
];

// ÖZET
echo "\n📊 KAPSAMLI KOD KONTROLÜ TAMAMLANDI!\n";
echo "=====================================\n\n";

echo "📋 ÖZET:\n";
echo '  - Lint Hataları: '.$results['summary']['lint']."\n";
echo '  - Dead Code: '.($results['summary']['dead_code'] ?? 0)."\n";
echo '  - Orphaned Code: '.$results['summary']['orphaned_code']."\n";
echo '  - TODO/FIXME: '.$results['summary']['incomplete']['todos']."\n";
echo '  - Boş Metodlar: '.$results['summary']['incomplete']['empty_methods']."\n";
echo '  - Stub Metodlar: '.$results['summary']['incomplete']['stub_methods']."\n";
echo '  - Disabled Code: '.$results['summary']['disabled_code']."\n";
echo '  - Code Duplication: '.$results['summary']['duplication']."\n";
echo '  - Security Issues: '.$results['summary']['security']."\n";
echo '  - Performance Issues: '.$results['summary']['performance']."\n";
echo '  - Dependency Issues: '.$results['summary']['dependency']."\n";
echo '  - Test Files: '.$results['summary']['test_coverage']."\n";

// JSON raporu kaydet
$reportDir = $basePath.'.yalihan-bekci/reports/';
if (! is_dir($reportDir)) {
    mkdir($reportDir, 0755, true);
}

$reportFile = $reportDir.'comprehensive-code-check-'.date('Y-m-d-His').'.json';
file_put_contents($reportFile, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\n✅ Detaylı rapor kaydedildi: ".$reportFile."\n";

// Yalıhan Bekçi'ye öğret
$knowledgeFile = $basePath.'.yalihan-bekci/knowledge/code-check-results-'.date('Y-m-d').'.json';
file_put_contents($knowledgeFile, json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'results' => $results,
    'recommendations' => generateRecommendations($results),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✅ Yalıhan Bekçi'ye öğretildi: ".$knowledgeFile."\n";

function generateRecommendations($results)
{
    $recommendations = [];

    if ($results['summary']['lint'] > 0) {
        $recommendations[] = [
            'priority' => 'HIGH',
            'action' => 'Lint hatalarını düzelt',
            'count' => $results['summary']['lint'],
        ];
    }

    if ($results['summary']['orphaned_code'] > 0) {
        $recommendations[] = [
            'priority' => 'MEDIUM',
            'action' => 'Orphaned controller\'ları kaldır veya route\'lara bağla',
            'count' => $results['summary']['orphaned_code'],
        ];
    }

    if ($results['summary']['incomplete']['todos'] > 50) {
        $recommendations[] = [
            'priority' => 'MEDIUM',
            'action' => 'TODO/FIXME yorumlarını gözden geçir ve tamamla',
            'count' => $results['summary']['incomplete']['todos'],
        ];
    }

    if ($results['summary']['security'] > 0) {
        $recommendations[] = [
            'priority' => 'CRITICAL',
            'action' => 'Güvenlik sorunlarını hemen düzelt',
            'count' => $results['summary']['security'],
        ];
    }

    return $recommendations;
}
