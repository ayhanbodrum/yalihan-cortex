<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;

class SystemMonitorController extends AdminController
{
    /**
     * MCP ve API health durumunu döner
     */
    public function index(Request $request)
    {
        // MCP process kontrolü (ps komutu ile)
        $mcpStatus = $this->getMcpStatus();
        // API health check (örnek endpoint)
        $apiStatus = $this->getApiStatus();
        // Self-healing ve hata logları
        $selfHealing = $this->getSelfHealingLog();
        // Son 10 hata
        $recentErrors = $this->getRecentErrors();

        // Genel durum hesapla
        $overall = $this->computeOverallStatus($mcpStatus, $apiStatus);

        return view('admin.ai-monitor.index', [
            'mcpStatus' => $mcpStatus,
            'apiStatus' => $apiStatus,
            'selfHealing' => $selfHealing,
            'recentErrors' => $recentErrors,
            'overall' => $overall,
        ]);
    }

    /**
     * Aktif MCP processlerini döner
     */
    protected function getMcpStatus()
    {
        $output = [];
        @exec('ps aux | grep -E "(mcp|yalihan)" | grep -v grep', $output);
        $list = collect($output)->map(function ($line) {
            $parts = preg_split('/\s+/', $line, 11);
            return [
                'user' => $parts[0] ?? '',
                'pid' => $parts[1] ?? '',
                'cpu' => $parts[2] ?? '',
                'mem' => $parts[3] ?? '',
                'command' => $parts[10] ?? '',
            ];
        })->filter(fn ($row) => !empty($row['pid']))->values();
        return $list;
    }

    /**
     * Kapsamlı sistem health check
     */
    protected function getApiStatus()
    {
        $endpoints = [
            'Laravel Health' => url('/'),
            'Admin Dashboard' => url('/admin/dashboard'),
            'API Status' => url('/api/health'),
        ];
        $results = [];
        foreach ($endpoints as $name => $url) {
            try {
                $start = microtime(true);
                $response = Http::timeout(2)->get($url);
                $latency = (int) ((microtime(true) - $start) * 1000);
                $results[$name] = [
                    'status' => $response->successful() ? 'OK' : 'ERROR',
                    'http_code' => $response->status(),
                    'latency_ms' => $latency,
                ];
            } catch (\Exception $e) {
                $results[$name] = [
                    'status' => 'DOWN',
                    'http_code' => null,
                    'latency_ms' => null,
                    'error' => $e->getMessage(),
                ];
            }
        }
        return $results;
    }

    /**
     * Self-healing loglarını okur
     */
    protected function getSelfHealingLog()
    {
        $path = base_path('.cursor/memory/context7-memory.md');
        if (is_file($path)) {
            $lines = @file($path, FILE_IGNORE_NEW_LINES) ?: [];
            return collect($lines)->slice(-10)->values();
        }
        return collect();
    }

    /**
     * Son 10 hata (örnek: EKOSISTEM-MONITOR-STATUS.md)
     */
    protected function getRecentErrors()
    {
        $path = base_path('docs/ai-training/EKOSISTEM-MONITOR-STATUS.md');
        if (is_file($path)) {
            $lines = @file($path, FILE_IGNORE_NEW_LINES) ?: [];
            return collect($lines)->filter(fn ($l) => str_contains($l, '|'))->take(15)->values();
        }
        return collect();
    }

    /**
     * MCP kullanım sayımlarını çıkar (komut stringinden basit sezgi)
     */
    protected function computeMcpUsageCounts($mcpStatus)
    {
        $counts = [
            'context7' => 0,
            'puppeteer' => 0,
            'memory' => 0,
            'filesystem' => 0,
            'yalihan-bekci' => 0,
            'laravel' => 0,
            'git' => 0,
            'ollama' => 0,
            'other' => 0,
        ];
        foreach ($mcpStatus as $row) {
            $cmd = $row['command'] ?? '';
            if (str_contains($cmd, 'mcp/context7')) $counts['context7']++;
            elseif (str_contains($cmd, 'mcp/puppeteer')) $counts['puppeteer']++;
            elseif (str_contains($cmd, 'mcp/memory')) $counts['memory']++;
            elseif (str_contains($cmd, 'mcp/filesystem')) $counts['filesystem']++;
            elseif (str_contains($cmd, 'yalihan-bekci/server/mcp-server.js')) $counts['yalihan-bekci']++;
            elseif (str_contains($cmd, 'laravel-mcp.js')) $counts['laravel']++;
            elseif (str_contains($cmd, 'git')) $counts['git']++;
            elseif (str_contains($cmd, 'ollama')) $counts['ollama']++;
            else $counts['other']++;
        }
        return $counts;
    }

    protected function computeOverallStatus($mcpStatus, $apiStatus)
    {
        $mcpCount = count($mcpStatus);
        $apiOk = collect($apiStatus)->filter(fn ($r) => ($r['status'] ?? $r) === 'OK')->count();
        $apiTotal = count($apiStatus);
        $level = 'green';
        if ($apiOk < $apiTotal) $level = 'yellow';
        if ($apiOk === 0 || $mcpCount === 0) $level = 'red';
        return [
            'level' => $level,
            'mcp_count' => $mcpCount,
            'api_ok' => $apiOk,
            'api_total' => $apiTotal,
            'mcp_usage' => $this->computeMcpUsageCounts($mcpStatus),
            // Yeni ekosistem analizleri
            'code_health' => $this->getCodeHealthStatus(),
            'duplicate_files' => $this->getDuplicateFiles(),
            'conflicting_routes' => $this->getConflictingRoutes(),
        ];
    }

    // ===== JSON Endpoints for Live Monitoring =====
    public function apiMcpStatus()
    {
        return response()->json([
            'data' => $this->getMcpStatus(),
            'overview' => $this->computeOverallStatus($this->getMcpStatus(), $this->getApiStatus()),
        ]);
    }

    public function apiApiStatus()
    {
        return response()->json(['data' => $this->getApiStatus()]);
    }

    public function apiSelfHealing()
    {
        return response()->json(['data' => $this->getSelfHealingLog()]);
    }

    public function apiRecentErrors()
    {
        return response()->json(['data' => $this->getRecentErrors()]);
    }

    // ===== EKOSİSTEM ANALİZ METODları =====

    /**
     * Context7 uyumlu kod sağlık durumu analizi
     * Bu sistem sadece önerilerde bulunur, kendi kafasına değişiklik yapmaz
     */
    protected function getCodeHealthStatus()
    {
        $issues = [];
        $suggestions = [];

        // Context7 kural uyumluluğunu kontrol et
        $context7Issues = $this->scanContext7Compliance();
        if (!empty($context7Issues)) {
            $issues[] = [
                'type' => 'context7_violations',
                'count' => count($context7Issues),
                'severity' => 'critical',
                'description' => 'Context7 kurallarına aykırı kullanımlar tespit edildi',
                'files' => array_slice($context7Issues, 0, 5),
                'suggestion' => 'Context7 kontrol scriptini çalıştırın: ./scripts/context7-check.sh --auto-fix'
            ];
            $suggestions[] = 'Context7 kurallarına uygun değil - lütfen ./scripts/context7-check.sh çalıştırın';
        }

        // Master dosyalara uygunluk kontrolü
        $masterComplianceIssues = $this->checkMasterDocumentCompliance();
        if (!empty($masterComplianceIssues)) {
            $issues[] = [
                'type' => 'master_document_violations',
                'count' => count($masterComplianceIssues),
                'severity' => 'high',
                'description' => 'Master dokümantasyon ile uyumsuzluk',
                'files' => $masterComplianceIssues,
                'suggestion' => 'Lütfen docs/context7/rules/context7-rules.md dosyasını kontrol edin'
            ];
            $suggestions[] = 'Kod master dokümanlara uygun değil - manuel kontrol gerekli';
        }

        // Tekrar eden fonksiyonları tespit et (sadece rapor et, değiştirme)
        $duplicateFunctions = $this->scanForDuplicateFunctions();
        if (!empty($duplicateFunctions)) {
            $issues[] = [
                'type' => 'duplicate_functions',
                'count' => count($duplicateFunctions),
                'severity' => 'medium',
                'description' => 'Tekrar eden fonksiyonlar tespit edildi',
                'files' => array_slice($duplicateFunctions, 0, 5),
                'suggestion' => 'Manuel olarak fonksiyonları birleştirmeyi düşünün'
            ];
            $suggestions[] = 'Duplike fonksiyonlar var - kod tekrarını azaltmayı düşünün';
        }

        // Kullanılmayan dosyaları tespit et (sadece öneri)
        $unusedFiles = $this->scanForUnusedFiles();
        if (!empty($unusedFiles)) {
            $issues[] = [
                'type' => 'unused_files',
                'count' => count($unusedFiles),
                'severity' => 'low',
                'description' => 'Kullanılmayan dosyalar tespit edildi',
                'files' => array_slice($unusedFiles, 0, 3),
                'suggestion' => 'Dosyaları manuel olarak kontrol edip silebilirsiniz'
            ];
            $suggestions[] = 'Kullanılmayan dosyalar var - temizlik önerilir';
        }

        // Database tutarlılık kontrolü
        $dbInconsistencies = $this->checkDatabaseConsistency();
        if (!empty($dbInconsistencies)) {
            $issues[] = [
                'type' => 'database_inconsistencies',
                'count' => count($dbInconsistencies),
                'severity' => 'high',
                'description' => 'Database şema tutarsızlıkları',
                'files' => $dbInconsistencies,
                'suggestion' => 'Migration dosyalarını ve model tanımlarını kontrol edin'
            ];
            $suggestions[] = 'Database tutarsızlıkları var - şema kontrolü gerekli';
        }

        return [
            'total_issues' => count($issues),
            'health_score' => $this->calculateHealthScore($issues),
            'issues' => $issues,
            'suggestions' => $suggestions,
            'compliance_status' => empty($context7Issues) ? 'compliant' : 'non_compliant',
            'action_required' => !empty($context7Issues) || !empty($masterComplianceIssues)
        ];
    }

    /**
     * Duplike dosyaları tespit et - sadece raporlar, değişiklik yapmaz
     */
    protected function getDuplicateFiles()
    {
        $duplicates = [];

        try {
            // Blade dosyalarını kontrol et
            $bladeFiles = [];
            $viewsPath = resource_path('views');

            if (is_dir($viewsPath)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($viewsPath)
                );

                foreach ($iterator as $file) {
                    if ($file->getExtension() === 'php' && strpos($file->getFilename(), '.blade.') !== false) {
                        $basename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                        $basename = str_replace('.blade', '', $basename);

                        if (!isset($bladeFiles[$basename])) {
                            $bladeFiles[$basename] = [];
                        }
                        $bladeFiles[$basename][] = [
                            'path' => $file->getPathname(),
                            'relative_path' => str_replace(base_path(), '', $file->getPathname()),
                            'size' => $file->getSize()
                        ];
                    }
                }

                foreach ($bladeFiles as $name => $files) {
                    if (count($files) > 1) {
                        $duplicates[] = [
                            'type' => 'blade_views',
                            'name' => $name,
                            'files' => $files,
                            'count' => count($files),
                            'suggestion' => 'Manuel olarak hangi dosyanın gerekli olduğunu kontrol edin',
                            'action' => 'no_auto_fix' // Otomatik düzeltme yapma
                        ];
                    }
                }
            }

            // Controller dosyalarını kontrol et
            $controllerDuplicates = $this->findDuplicateControllers();
            $duplicates = array_merge($duplicates, $controllerDuplicates);

            // Model dosyalarını kontrol et
            $modelDuplicates = $this->findDuplicateModels();
            $duplicates = array_merge($duplicates, $modelDuplicates);

        } catch (\Exception $e) {
            $duplicates[] = [
                'type' => 'scan_error',
                'name' => 'File scan error',
                'error' => $e->getMessage(),
                'suggestion' => 'Dosya tarama hatası - manuel kontrol gerekli'
            ];
        }

        return $duplicates;
    }

    /**
     * Duplike controller'ları bul
     */
    protected function findDuplicateControllers()
    {
        $duplicates = [];
        $controllers = [];

        $appPath = app_path();
        if (is_dir($appPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($appPath)
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() === 'php' && strpos($file->getFilename(), 'Controller.php') !== false) {
                    $basename = str_replace('Controller.php', '', $file->getFilename());

                    if (!isset($controllers[$basename])) {
                        $controllers[$basename] = [];
                    }
                    $controllers[$basename][] = [
                        'path' => $file->getPathname(),
                        'relative_path' => str_replace(base_path(), '', $file->getPathname())
                    ];
                }
            }

            foreach ($controllers as $name => $files) {
                if (count($files) > 1) {
                    $duplicates[] = [
                        'type' => 'controllers',
                        'name' => $name . 'Controller',
                        'files' => $files,
                        'count' => count($files),
                        'suggestion' => 'Context7 kurallarına göre controller ismi birleştirilmeli',
                        'context7_rule' => 'Aynı işlevsellik için farklı controller isimleri kullanılamaz'
                    ];
                }
            }
        }

        return $duplicates;
    }

    /**
     * Duplike model'leri bul
     */
    protected function findDuplicateModels()
    {
        $duplicates = [];
        $models = [];

        // App/Models altını kontrol et
        $modelsPath = app_path('Models');
        if (is_dir($modelsPath)) {
            $files = glob($modelsPath . '/*.php');
            foreach ($files as $file) {
                $basename = str_replace('.php', '', basename($file));
                $models[$basename][] = [
                    'path' => $file,
                    'relative_path' => str_replace(base_path(), '', $file)
                ];
            }
        }

        // Modules altındaki model'leri kontrol et
        $modulesPath = app_path('Modules');
        if (is_dir($modulesPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($modulesPath)
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() === 'php' && strpos($file->getPath(), '/Models') !== false) {
                    $basename = str_replace('.php', '', $file->getFilename());

                    if (!isset($models[$basename])) {
                        $models[$basename] = [];
                    }
                    $models[$basename][] = [
                        'path' => $file->getPathname(),
                        'relative_path' => str_replace(base_path(), '', $file->getPathname())
                    ];
                }
            }
        }

        foreach ($models as $name => $files) {
            if (count($files) > 1) {
                $duplicates[] = [
                    'type' => 'models',
                    'name' => $name,
                    'files' => $files,
                    'count' => count($files),
                    'suggestion' => 'Context7 kurallarına göre model konumu standartlaştırılmalı',
                    'context7_rule' => 'Modeller app/Models veya modül altında bulunmalı'
                ];
            }
        }

        return $duplicates;
    }

    /**
     * Çakışan rotaları tespit et
     */
    protected function getConflictingRoutes()
    {
        try {
            $routes = \Illuminate\Support\Facades\Route::getRoutes();
            $conflicts = [];
            $uriMap = [];

            foreach ($routes as $route) {
                $uri = $route->uri();
                $methods = implode('|', $route->methods());
                $key = $methods . ':' . $uri;

                if (!isset($uriMap[$key])) {
                    $uriMap[$key] = [];
                }

                $uriMap[$key][] = [
                    'name' => $route->getName(),
                    'action' => $route->getActionName(),
                    'methods' => $route->methods()
                ];
            }

            foreach ($uriMap as $key => $routeList) {
                if (count($routeList) > 1) {
                    $conflicts[] = [
                        'uri_methods' => $key,
                        'routes' => $routeList,
                        'count' => count($routeList)
                    ];
                }
            }

            return $conflicts;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Tekrar eden fonksiyonları tara
     */
    protected function scanForDuplicateFunctions()
    {
        $functions = [];
        $duplicates = [];

        $phpFiles = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(app_path())
        );

        foreach ($phpFiles as $file) {
            if ($file->getExtension() === 'php') {
                $content = @file_get_contents($file->getPathname());
                if ($content) {
                    // Fonksiyon tanımlarını bul
                    preg_match_all('/(?:public|protected|private)?\s*function\s+(\w+)\s*\(/', $content, $matches);

                    if (!empty($matches[1])) {
                        foreach ($matches[1] as $functionName) {
                            if (!isset($functions[$functionName])) {
                                $functions[$functionName] = [];
                            }
                            $functions[$functionName][] = $file->getPathname();
                        }
                    }
                }
            }
        }

        foreach ($functions as $name => $files) {
            if (count($files) > 1 && $name !== '__construct' && $name !== '__destruct') {
                $duplicates[] = [
                    'function' => $name,
                    'files' => $files,
                    'count' => count($files)
                ];
            }
        }

        return array_slice($duplicates, 0, 10); // İlk 10 tanesi
    }

    /**
     * Kullanılmayan dosyaları tara
     */
    protected function scanForUnusedFiles()
    {
        // Basit implementasyon - geliştirebilir
        $unused = [];

        // Test ve example dosyalarını kontrol et
        $testDirs = [
            base_path('tests'),
            base_path('example'),
            base_path('samples')
        ];

        foreach ($testDirs as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '/*.php');
                foreach ($files as $file) {
                    $unused[] = $file;
                }
            }
        }

        return array_slice($unused, 0, 5);
    }

    /**
     * Eksik bağımlılıkları tara
     */
    protected function scanForMissingDependencies()
    {
        $missing = [];

        // Yaygın eksik dosyaları kontrol et
        $requiredFiles = [
            base_path('ai/prompts/talep-analizi-legacy.prompt.md'),
            base_path('.cursor/memory/context7-memory.md'),
            resource_path('js/app.js'),
            resource_path('css/app.css')
        ];

        foreach ($requiredFiles as $file) {
            if (!file_exists($file)) {
                $missing[] = str_replace(base_path(), '', $file);
            }
        }

        return $missing;
    }

    /**
     * Context7 kurallarına uygun sağlık skoru hesapla
     */
    protected function calculateHealthScore($issues)
    {
        $score = 100;

        foreach ($issues as $issue) {
            $penalty = 0;
            switch ($issue['severity']) {
                case 'critical':
                    $penalty = $issue['count'] * 20; // Context7 ihlalleri kritik
                    break;
                case 'high':
                    $penalty = $issue['count'] * 10;
                    break;
                case 'medium':
                    $penalty = $issue['count'] * 5;
                    break;
                case 'low':
                    $penalty = $issue['count'] * 2;
                    break;
            }
            $score -= $penalty;
        }

        return max(0, min(100, $score));
    }

    /**
     * Context7 kural uyumluluğunu kontrol et
     * Sadece tespit eder, değişiklik yapmaz
     */
    protected function scanContext7Compliance()
    {
        $violations = [];

        // Test ortamında kabuk komutlarını çalıştırma
        if (app()->environment('testing')) {
            return $violations;
        }

        // Yasaklı alan adlarını kontrol et
        $forbiddenFields = [
            'status' => 'status kullanılmalı',
            'is_active' => 'status kullanılmalı',
            'aktif' => 'status kullanılmalı',
            'ad_soyad' => 'tam_ad kullanılmalı',
            'full_name' => 'name kullanılmalı',
            'il' => 'il kullanılmalı',
            'region_id' => 'il_id kullanılmalı'
        ];

        foreach ($forbiddenFields as $forbidden => $suggestion) {
            // PHP dosyalarında kontrol et
            $result = [];
            @exec("grep -R -n --include='*.php' --include='*.blade.php' " . escapeshellarg($forbidden) . " app resources/views 2>/dev/null", $result);

            if (!empty($result)) {
                $violations[] = [
                    'type' => 'forbidden_field',
                    'field' => $forbidden,
                    'suggestion' => $suggestion,
                    'files' => array_slice($result, 0, 3)
                ];
            }
        }

        return $violations;
    }

    /**
     * Master dokümantasyon uyumluluğunu kontrol et
     */
    protected function checkMasterDocumentCompliance()
    {
        $issues = [];

        // Context7 rules dosyasını kontrol et
        $rulesPath = base_path('docs/context7/rules/context7-rules.md');
        if (!file_exists($rulesPath)) {
            $issues[] = [
                'type' => 'missing_rules',
                'file' => $rulesPath,
                'description' => 'Context7 rules dosyası bulunamadı'
            ];
        }

        // Migration dosyalarının tutarlılığını kontrol et
        $migrationIssues = $this->checkMigrationConsistency();
        if (!empty($migrationIssues)) {
            $issues = array_merge($issues, $migrationIssues);
        }

        return $issues;
    }

    /**
     * Migration tutarlılığını kontrol et
     */
    protected function checkMigrationConsistency()
    {
        $issues = [];

        // Migration dosyalarını tara
        $migrationPath = database_path('migrations');
        if (is_dir($migrationPath)) {
            $files = glob($migrationPath . '/*.php');

            foreach ($files as $file) {
                $content = @file_get_contents($file);
                if ($content) {
                    // Context7 yasaklı alanları kontrol et
                    if (preg_match('/\$table->.*\(\'(status|is_active|aktif|ad_soyad|region_id)\'/', $content, $matches)) {
                        $issues[] = [
                            'type' => 'migration_violation',
                            'file' => basename($file),
                            'field' => $matches[1],
                            'description' => 'Context7 yasaklı alan kullanımı'
                        ];
                    }
                }
            }
        }

        return $issues;
    }

    /**
     * Database tutarlılığını kontrol et
     */
    protected function checkDatabaseConsistency()
    {
        $issues = [];

        try {
            // Model-database uyumluluğunu kontrol et
            $models = ['Ilan', 'Kisi', 'User'];

            foreach ($models as $modelName) {
                $modelClass = "App\\Models\\{$modelName}";
                if (class_exists($modelClass)) {
                    $model = new $modelClass;
                    $tableName = $model->getTable();

                    // Tablonun var olup olmadığını kontrol et
                    if (Schema::hasTable($tableName)) {
                        $columns = Schema::getColumnListing($tableName);
                        $fillable = $model->getFillable();

                        // Fillable alanların database'de olup olmadığını kontrol et
                        foreach ($fillable as $field) {
                            if (!in_array($field, $columns)) {
                                $issues[] = [
                                    'type' => 'missing_column',
                                    'model' => $modelName,
                                    'table' => $tableName,
                                    'field' => $field,
                                    'description' => "Model'de tanımlı alan database'de yok"
                                ];
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $issues[] = [
                'type' => 'database_error',
                'description' => 'Database bağlantı hatası: ' . $e->getMessage()
            ];
        }

        return $issues;
    }

    // Yeni JSON endpoint'ler
    public function apiCodeHealth()
    {
        try {
            return response()->json(['data' => $this->getCodeHealthStatus()]);
        } catch (\Throwable $e) {
            return response()->json([
                'data' => [
                    'total_issues' => 0,
                    'health_score' => 0,
                    'issues' => [],
                    'suggestions' => [],
                    'compliance_status' => 'unknown',
                    'action_required' => false,
                ],
                'error' => 'code_health_error: ' . $e->getMessage(),
            ]);
        }
    }

    public function apiDuplicateFiles()
    {
        return response()->json(['data' => $this->getDuplicateFiles()]);
    }

    public function apiConflictingRoutes()
    {
        return response()->json(['data' => $this->getConflictingRoutes()]);
    }

    /**
     * Yönetici sayfalarının temel sağlık kontrolü
     * Not: Sadece GET ve içerik işaretlerini doğrular; state değiştirmez
     */
    public function apiPagesHealth(Request $request)
    {
        return response()->json(['data' => $this->getPagesHealth($request)]);
    }

    /**
     * Belirli admin sayfalarında beklenen işaretleri arar
     * - Mevcut oturumun Cookie başlığını ileterek kimliği korur
     */
    protected function getPagesHealth(Request $request)
    {
        $pages = [
            [
                'name' => 'Admin Dashboard',
                'url' => url('/admin/dashboard'),
                'markers' => ['aiStatusWidget', 'AI Destekli', 'Context7'],
            ],
            [
                'name' => 'AI Monitor',
                'url' => route('admin.ai-monitor.index', absolute: false),
                'markers' => ['AI Monitoring', 'Context7 Uyumluluk', 'MCP Server Durumu'],
            ],
            [
                'name' => 'Tema Önizleme',
                'url' => route('admin.theme.preview', absolute: false),
                'markers' => ['Theme', 'Preview'],
            ],
        ];

        $cookie = $request->header('Cookie');
        $agent = 'PagesHealthBot/1.0 (+context7)';
        $results = [];

        foreach ($pages as $page) {
            $status = [
                'name' => $page['name'],
                'url' => $page['url'],
                'status' => 'UNKNOWN',
                'http_code' => null,
                'latency_ms' => null,
                'markers_found' => false,
                'missing_markers' => $page['markers'],
            ];

            try {
                $start = microtime(true);
                $res = Http::withHeaders(array_filter([
                    'Cookie' => $cookie,
                    'User-Agent' => $agent,
                    'X-Requested-With' => 'XMLHttpRequest',
                ]))->timeout(3)->get($page['url']);
                $status['latency_ms'] = (int) ((microtime(true) - $start) * 1000);
                $status['http_code'] = $res->status();
                $status['status'] = $res->successful() ? 'OK' : ($res->status() === 302 ? 'REDIRECT' : 'ERROR');

                $body = $res->body() ?? '';
                $missing = [];
                $foundAny = false;
                foreach ($page['markers'] as $m) {
                    if (stripos($body, $m) !== false) {
                        $foundAny = true;
                    } else {
                        $missing[] = $m;
                    }
                }
                $status['markers_found'] = $foundAny;
                $status['missing_markers'] = $missing;
            } catch (\Throwable $e) {
                $status['status'] = 'DOWN';
                $status['error'] = $e->getMessage();
            }

            $results[] = $status;
        }

        return $results;
    }

        /**
         * Context7 otomatik düzeltme çalıştır - sadece öneride bulunur
         */
        public function runContext7Fix()
        {
            try {
                // Context7 kurallarına göre - sadece öneri, otomatik değişiklik yapmaz
                $suggestions = [
                    'Terminal komutunu çalıştırın: ./scripts/context7-check.sh --auto-fix',
                    'Migration kontrol: php artisan context7:validate-migration --all',
                    'Cache temizleme: php artisan view:clear && php artisan config:clear',
                    'Manuel kontrol gerekli - otomatik değişiklik yapılmadı'
                ];

                return response()->json([
                    'success' => true,
                    'message' => 'Context7 kontrol önerileri hazırlandı',
                    'suggestions' => $suggestions,
                    'action' => 'manual_required'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Context7 kontrol hatası: ' . $e->getMessage(),
                    'suggestion' => 'Lütfen manuel olarak ./scripts/context7-check.sh çalıştırın'
                ]);
            }
        }

        /**
         * Context7 önerisini değerlendir - otomatik uygulama yapmaz
         */
        public function applySuggestion(Request $request)
        {
            $suggestion = $request->input('suggestion');
            $index = $request->input('index');

            // Context7 kurallarına göre - sadece bilgilendirme
            $response = [
                'success' => false,
                'manual_required' => true,
                'message' => 'Bu öneri manuel olarak uygulanmalıdır',
                'instructions' => []
            ];

            if (str_contains($suggestion, 'Context7')) {
                $response['instructions'] = [
                    '1. Terminal açın',
                    '2. Komutu çalıştırın: ./scripts/context7-check.sh --auto-fix',
                    '3. Hataları gözden geçirin',
                    '4. Manuel düzeltme yapın'
                ];
            } elseif (str_contains($suggestion, 'duplik')) {
                $response['instructions'] = [
                    '1. Duplike dosyaları manuel kontrol edin',
                    '2. Hangi dosyanın gerekli olduğunu belirleyin',
                    '3. Gereksiz dosyayı silin',
                    '4. Kodları birleştirin'
                ];
            } elseif (str_contains($suggestion, 'database')) {
                $response['instructions'] = [
                    '1. Migration dosyalarını kontrol edin',
                    '2. Model tanımlarını gözden geçirin',
                    '3. Schema tutarlılığını sağlayın',
                    '4. Testleri çalıştırın'
                ];
            }

            return response()->json($response);
        }

        /**
         * Context7 kurallarını getir
         */
        public function getContext7Rules()
        {
            $rulesPath = base_path('docs/context7/rules/context7-rules.md');

            if (file_exists($rulesPath)) {
                $content = file_get_contents($rulesPath);

                // Önemli kuralları çıkar
                $importantRules = [
                    'Yasaklı Alan Adları' => [
                        'status → status kullanılmalı',
                        'is_active → status kullanılmalı',
                        'aktif → status kullanılmalı',
                        'ad_soyad → tam_ad kullanılmalı',
                        'region_id → il_id kullanılmalı'
                    ],
                    'Zorunlu Kurallar' => [
                        'AI asla kendi kafasına göre tablo yaratamaz',
                        'Master dosyalara sadakat zorunludur',
                        'Context7 kuralları mutlaka referans alınmalı',
                        'Otomatik değişiklik yapmak yasaktır'
                    ],
                    'Düzeltme Komutları' => [
                        './scripts/context7-check.sh --auto-fix',
                        'php artisan context7:validate-migration --all',
                        'php artisan view:clear && php artisan config:clear'
                    ]
                ];

                return response()->json([
                    'success' => true,
                    'rules' => $importantRules,
                    'file_exists' => true,
                    'last_modified' => date('Y-m-d H:i:s', filemtime($rulesPath))
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Context7 rules dosyası bulunamadı',
                'file_exists' => false,
                'path' => $rulesPath
            ]);
        }
}
