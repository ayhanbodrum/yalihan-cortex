<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class AnalyzePagesComplete extends Command
{
    protected $signature = 'analyze:pages-complete
                           {--page= : Specific page to analyze}
                           {--format=console : Output format (console, json, html)}
                           {--output= : Output file path}';

    protected $description = 'Complete analysis of all admin pages in the system';

    protected $results = [];
    protected $scores = [];

    public function handle()
    {
        $this->info('🔍 EmlakPro Complete Page Analysis Starting...');
        $this->newLine();

        $specificPage = $this->option('page');

        if ($specificPage) {
            $this->analyzeSinglePage($specificPage);
        } else {
            $this->analyzeAllPages();
        }

        // Save all analysis results to JSON file at the end
        $this->saveAnalysisToJSON();

        $this->generateReport();
        return 0;
    }

    protected function analyzeAllPages()
    {
        // Tüm admin controller'ları keşfet
        $adminPages = $this->discoverAllAdminPages();

        $this->info("📊 Keşfedilen toplam sayfa sayısı: " . count($adminPages));
        $this->newLine();

        $progressBar = $this->output->createProgressBar(count($adminPages));
        $progressBar->start();

        foreach ($adminPages as $pageKey => $pageData) {
            $this->analyzePage($pageData['name'], $pageData['controller'], $pageData);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
    }

    protected function discoverAllAdminPages()
    {
        $pages = [];

        // 1. Controller dosyalarından keşfet
        $controllerPath = app_path('Http/Controllers/Admin');
        if (File::exists($controllerPath)) {
            $controllerFiles = File::files($controllerPath);

            foreach ($controllerFiles as $file) {
                $controllerName = $file->getFilenameWithoutExtension();
                if (Str::endsWith($controllerName, 'Controller')) {
                    $pages[] = [
                        'name' => $this->generatePageName($controllerName),
                        'controller' => $controllerName,
                        'type' => 'Standard Admin Controller',
                        'file_path' => $file->getPathname(),
                        'route_prefix' => $this->guessRoutePrefix($controllerName)
                    ];
                }
            }
        }

        // 2. Modül controller'larından keşfet
        $modulesPath = app_path('Modules');
        if (File::exists($modulesPath)) {
            $modules = File::directories($modulesPath);

            foreach ($modules as $modulePath) {
                $adminControllerPath = $modulePath . '/Controllers/Admin';
                if (File::exists($adminControllerPath)) {
                    $controllerFiles = File::files($adminControllerPath);

                    foreach ($controllerFiles as $file) {
                        $controllerName = $file->getFilenameWithoutExtension();
                        if (Str::endsWith($controllerName, 'Controller')) {
                            $moduleName = basename($modulePath);
                            $pages[] = [
                                'name' => "$moduleName - " . $this->generatePageName($controllerName),
                                'controller' => $controllerName,
                                'type' => 'Module Controller',
                                'module' => $moduleName,
                                'file_path' => $file->getPathname(),
                                'route_prefix' => $this->guessModuleRoutePrefix($moduleName, $controllerName)
                            ];
                        }
                    }
                }
            }
        }

        // 3. Route dosyasından blade view'ları keşfet
        $routePages = $this->discoverRoutePages();
        $pages = array_merge($pages, $routePages);

        return $pages;
    }

    protected function discoverRoutePages()
    {
        $pages = [];

        // Admin route dosyasını oku
        $routeFile = base_path('routes/admin.php');
        if (File::exists($routeFile)) {
            $routeContent = File::get($routeFile);

            // Route::get patterns'ını bul (view return eden)
            preg_match_all('/Route::get\([\'"]([^"\']+)[\'"].*?function\s*\(\)\s*\{.*?return\s+view\([\'"]([^"\']+)[\'"].*?\}.*?\)->name\([\'"]([^"\']+)[\'"]\)/s', $routeContent, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $uri = $match[1];
                $viewName = $match[2];
                $routeName = $match[3];

                $pages[] = [
                    'name' => $this->generatePageNameFromRoute($uri),
                    'controller' => 'Closure/View',
                    'type' => 'Route-View',
                    'uri' => $uri,
                    'view' => $viewName,
                    'route_name' => $routeName
                ];
            }

            // Bilinen redirect route'larını manuel olarak ekle
            $knownRedirects = [
                '/ai-redirect' => 'ai.redirect',
                '/analytics-redirect' => 'analytics.redirect',
                '/raporlar' => 'raporlar.redirect',
                '/customers' => 'customers.redirect'
            ];

            foreach ($knownRedirects as $uri => $routeName) {
                if (strpos($routeContent, $uri) !== false) {
                    $pages[] = [
                        'name' => $this->generatePageNameFromRoute($uri),
                        'controller' => 'Redirect',
                        'type' => 'Route-Redirect',
                        'uri' => $uri,
                        'route_name' => $routeName
                    ];
                    $this->info("Redirect route eklendi: {$uri} -> {$routeName}");
                }
            }
        }

        return $pages;
    }
    protected function generatePageName($controllerName)
    {
        // XyzController -> Xyz Management/Dashboard
        $baseName = str_replace('Controller', '', $controllerName);

        // 🧠 AKILLI MAPPING - Ana Emlak Sayfaları Öncelikli
        $specialMappings = [
            // 🏠 ANA EMLAK SİSTEMLERİ - ÖNCELİKLİ
            'IlanController' => 'İlan Yönetimi 🏠', // Ana ilan sistemi
            'SmartIlanController' => 'Akıllı İlan Sistemi 🤖', // AI destekli ilan
            'IlanKategoriController' => 'İlan Kategorileri 📂',
            'IlanSegmentController' => 'İlan Segmentleri 🎯',
            'TalepController' => 'Talep Yönetimi 📋', // Müşteri talepleri
            'EslesmeController' => 'Talep-İlan Eşleştirme 🔄', // AI matching

            // 👥 MÜŞTERİ & DANIŞMAN YÖNETİMİ
            'KisiController' => 'Kişiler Yönetimi 👤', // Ana müşteri sistemi
            'DanismanController' => 'Danışman Yönetimi 👨‍💼', // Danışman sistemi
            'DanismanAIController' => 'Danışman AI Sistemi 🤖',
            'UserController' => 'Kullanıcı Yönetimi 👥', // Sistem kullanıcıları
            'CustomerProfileController' => 'Müşteri Profilleri 📄',
            'KisiNotController' => 'Müşteri Notları 📝',
            'BulkKisiController' => 'Toplu Kişi İşlemleri 📊',

            // 🤖 AI & ANALYTICS
            'AnalyticsController' => 'Analitik Dashboard 📊',
            'AISettingsController' => 'AI Yapılandırması ⚙️',
            'PageAnalyzerController' => 'Sayfa Analiz Sistemi 🔍',

            // 🏢 SİSTEM YÖNETİMİ
            'CRMController' => 'CRM Dashboard 🏢',
            'AdresYonetimiController' => 'Adres Yönetimi 📍',
            'LocationController' => 'Lokasyon Sistemi 🗺️',
            'MyListingsController' => 'İlanlarım 📋',

            // 📱 İLETİŞİM & ENTEGRASYON
            'TelegramBotController' => 'Telegram Bot Manager 📱',
            'NotificationController' => 'Bildirim Sistemi 🔔',

            // ⚙️ SİSTEM AYARLARI
            'AyarlarController' => 'Sistem Ayarları ⚙️',
            'PerformanceController' => 'Performans İzleme 📈',
            'BlogController' => 'Blog Yönetimi ✍️',
            'OzellikController' => 'Özellik Yönetimi 🏷️',
            'MapController' => 'Map Interface',
            'SmartCalculatorController' => 'Smart Calculator',
            'SmartIlanController' => 'Akıllı İlan Sistemi 🤖',
            'FeatureCategoryController' => 'Feature Category Management'
        ];

        return $specialMappings[$controllerName] ?? Str::title(str_replace('_', ' ', Str::snake($baseName))) . ' Management';
    }

    protected function getPageCategory($pageName, $controllerName = '')
    {
        // AI Sistemi kategorisi
        if (
            Str::contains($pageName, ['AI', 'Yapay', 'Akıllı', 'Analiz', 'Bot']) ||
            Str::contains($controllerName, ['AI', 'Smart', 'Bot', 'Analytics'])
        ) {
            return 'AI Sistemi';
        }

        // İlan Yönetimi kategorisi ve alt kategorileri
        if (
            Str::contains($pageName, ['İlan', 'Listing', 'Property']) ||
            Str::contains($controllerName, ['Ilan', 'Listing', 'Property'])
        ) {

            // Alt kategoriler
            if (Str::contains($pageName, ['Kategori', 'Category'])) return 'İlan Yönetimi - Kategoriler';
            if (Str::contains($pageName, ['Segment', 'Tip'])) return 'İlan Yönetimi - Segmentler';
            if (Str::contains($pageName, ['İlanlarım', 'My'])) return 'İlan Yönetimi - Kişisel İlanlar';

            return 'İlan Yönetimi - Genel';
        }

        // Takım Yönetimi kategorisi ve alt kategorileri
        if (
            Str::contains($pageName, ['Danışman', 'Kullanıcı', 'User', 'Consultant', 'Team']) ||
            Str::contains($controllerName, ['Danisman', 'User', 'Consultant'])
        ) {

            if (Str::contains($pageName, ['Danışman', 'Consultant'])) return 'Takım Yönetimi - Danışmanlar';
            if (Str::contains($pageName, ['Kullanıcı', 'User'])) return 'Takım Yönetimi - Sistem Kullanıcıları';
            if (Str::contains($pageName, ['Profil', 'Profile'])) return 'Takım Yönetimi - Profiller';

            return 'Takım Yönetimi - Genel';
        }

        // CRM kategorisi ve alt kategorileri
        if (
            Str::contains($pageName, ['CRM', 'Müşteri', 'Customer', 'Kişi', 'Talep', 'Request']) ||
            Str::contains($controllerName, ['CRM', 'Customer', 'Kisi', 'Talep'])
        ) {

            if (Str::contains($pageName, ['Talep', 'Request'])) return 'CRM - Talep Yönetimi';
            if (Str::contains($pageName, ['Eşleştirme', 'Match'])) return 'CRM - Talep-İlan Eşleştirme';
            if (Str::contains($pageName, ['Not', 'Note'])) return 'CRM - Müşteri Notları';
            if (Str::contains($pageName, ['Profil', 'Profile'])) return 'CRM - Müşteri Profilleri';
            if (Str::contains($pageName, ['Toplu', 'Bulk'])) return 'CRM - Toplu İşlemler';

            return 'CRM - Genel';
        }

        // Blog Yönetimi kategorisi
        if (
            Str::contains($pageName, ['Blog', 'Makale', 'Article', 'Content']) ||
            Str::contains($controllerName, ['Blog', 'Article'])
        ) {
            return 'Blog Yönetimi';
        }

        // Adres Yönetimi kategorisi
        if (
            Str::contains($pageName, ['Adres', 'Address', 'Lokasyon', 'Location', 'Map']) ||
            Str::contains($controllerName, ['Adres', 'Location', 'Map'])
        ) {
            return 'Adres Yönetimi';
        }

        // Analytics kategorisi
        if (
            Str::contains($pageName, ['Analitik', 'Analytics', 'Rapor', 'Report', 'İstatistik', 'Statistics']) ||
            Str::contains($controllerName, ['Analytics', 'Report', 'Performance'])
        ) {
            return 'Analytics';
        }

        // Sistem Ayarları kategorisi
        if (
            Str::contains($pageName, ['Ayar', 'Settings', 'Config', 'Özellik', 'Feature']) ||
            Str::contains($controllerName, ['Ayarlar', 'Settings', 'Config', 'Ozellik'])
        ) {
            return 'Sistem Ayarları';
        }

        // Bildirim Sistemi
        if (
            Str::contains($pageName, ['Bildirim', 'Notification']) ||
            Str::contains($controllerName, ['Notification'])
        ) {
            return 'Bildirim Sistemi';
        }

        return 'Diğer';
    }

    protected function getScoreColor($score)
    {
        if ($score >= 8) return 'green';
        if ($score >= 6) return 'yellow';
        return 'red';
    }

    protected function generatePageNameFromRoute($uri)
    {
        $segments = explode('/', trim($uri, '/'));
        $lastSegment = end($segments);

        return Str::title(str_replace(['-', '_'], ' ', $lastSegment));
    }

    protected function guessRoutePrefix($controllerName)
    {
        $mapping = [
            'TelegramBotController' => 'telegram-bot',
            'AdresYonetimiController' => 'adres-yonetimi',
            'MyListingsController' => 'my-listings',
            'AnalyticsController' => 'analytics',
            'NotificationController' => 'notifications',
            'AISettingsController' => 'ai-settings',
            'CRMController' => 'crm',
            'IlanController' => 'ilanlar',
            'TalepController' => 'talepler',
            'BlogController' => 'blog',
            'PerformanceController' => 'performance',
            'FeatureCategoryController' => 'feature-categories'
        ];

        return $mapping[$controllerName] ?? Str::kebab(str_replace('Controller', '', $controllerName));
    }

    protected function guessModuleRoutePrefix($moduleName, $controllerName)
    {
        return Str::kebab($moduleName) . '/' . $this->guessRoutePrefix($controllerName);
    }

    protected function analyzeSinglePage($page)
    {
        $controllerMap = [
            'telegram-bot' => 'TelegramBotController',
            'adres-yonetimi' => 'AdresYonetimiController',
            'my-listings' => 'MyListingsController',
            'analytics' => 'AnalyticsController',
            'notifications' => 'NotificationController',
        ];

        if (!isset($controllerMap[$page])) {
            $this->error("❌ Unknown page: {$page}");
            return;
        }

        $this->analyzePage($page, $controllerMap[$page]);
    }

    protected function analyzePage($pageName, $controller, $pageData = [])
    {
        $analysis = [
            'page' => $pageName,
            'controller' => $controller,
            'type' => $pageData['type'] ?? 'Unknown',
            'controller_analysis' => $this->analyzeController($controller, $pageData),
            'route_analysis' => $this->analyzeRoutes($pageName, $pageData),
            'view_analysis' => $this->analyzeView($pageName, $pageData),
            'context7_compliance' => $this->checkContext7Compliance($pageName),
            'accessibility' => $this->analyzeAccessibility($pageData),
            'performance' => $this->analyzePerformance($pageData),
            'security' => $this->analyzeSecurity($pageData),
            'score' => 0,
            'issues' => [],
            'recommendations' => []
        ];

        // Calculate overall score
        $analysis['score'] = $this->calculateScore($analysis);

        // Determine severity based on score
        $analysis['severity'] = $this->determineSeverity($analysis['score']);

        $pageKey = Str::slug($pageName);
        $this->results[$pageKey] = $analysis;
        $this->scores[$pageKey] = $analysis['score'];
    }

    protected function analyzeAccessibility($pageData)
    {
        $score = 7; // Base accessibility score
        $issues = [];

        // Check if it's a modern page type
        if (isset($pageData['type']) && $pageData['type'] === 'Route-View') {
            $score += 1; // Modern view-based pages tend to have better accessibility
        }

        return [
            'score' => $score,
            'issues' => $issues,
            'recommendations' => [
                'Ensure proper ARIA labels',
                'Add keyboard navigation support',
                'Use semantic HTML elements'
            ]
        ];
    }

    protected function analyzePerformance($pageData)
    {
        $score = 6; // Base performance score
        $issues = [];

        if (isset($pageData['type']) && $pageData['type'] === 'Module Controller') {
            $score += 1; // Module controllers are typically better organized
        }

        return [
            'score' => $score,
            'issues' => $issues,
            'recommendations' => [
                'Implement caching strategies',
                'Optimize database queries',
                'Use lazy loading for heavy components'
            ]
        ];
    }

    protected function analyzeSecurity($pageData)
    {
        $score = 8; // Base security score (Laravel defaults are good)
        $issues = [];

        return [
            'score' => $score,
            'issues' => $issues,
            'recommendations' => [
                'Ensure CSRF protection is enabled',
                'Validate all user inputs',
                'Use proper authorization middleware'
            ]
        ];
    }

    protected function determineSeverity($score)
    {
        if ($score >= 8) return 'success';
        if ($score >= 6) return 'warning';
        if ($score >= 4) return 'danger';
        return 'critical';
    }

    protected function analyzeController($controller, $pageData = [])
    {
        // Redirect route'ları için özel durum
        if (
            $controller === 'Redirect' || $controller === 'Closure/Redirect' ||
            (isset($pageData['type']) && $pageData['type'] === 'Route-Redirect')
        ) {
            return [
                'exists' => true,
                'methods' => ['redirect'],
                'method_count' => 1,
                'issues' => [],
                'score' => 10,
                'note' => 'Route redirect - controller file not required'
            ];
        }

        // Determine controller path based on type
        if (isset($pageData['file_path'])) {
            $controllerPath = $pageData['file_path'];
        } elseif (isset($pageData['module'])) {
            $controllerPath = app_path("Modules/{$pageData['module']}/Controllers/Admin/{$controller}.php");
        } else {
            $controllerPath = app_path("Http/Controllers/Admin/{$controller}.php");
        }

        if (!File::exists($controllerPath)) {
            return [
                'exists' => false,
                'methods' => [],
                'issues' => ['Controller file not found'],
                'score' => 1
            ];
        }

        $content = File::get($controllerPath);
        $methods = $this->extractMethods($content);

        $issues = [];
        $score = 5; // Base score

        // 🧠 AKILLI METHOD ANALİZİ

        // Sayfa tipine göre gerekli methodları belirle
        $requiredMethods = $this->getRequiredMethodsForPage($pageData['name'] ?? '');
        $implementedRequired = array_intersect($requiredMethods, array_keys($methods));

        // Eksik methodları tespit et
        foreach ($requiredMethods as $method) {
            if (!in_array($method, array_keys($methods))) {
                $priority = $this->getMethodPriority($method, $pageData['name'] ?? '');
                $issues[] = "Missing {$priority} method: {$method}";
            }
        }

        // Temel CRUD skorlaması
        $score += count($implementedRequired);

        // 🎯 ÖZELLEŞTİRİLMİŞ METHOD KONTROLÜ

        // AI sayfaları için özel methodlar
        if ($this->isAIPage($pageData['name'] ?? '')) {
            $aiMethods = ['chat', 'analyze', 'suggest', 'health'];
            $implementedAI = array_intersect($aiMethods, array_keys($methods));
            $score += count($implementedAI) * 0.5; // AI methodları bonus
        }

        // Ana emlak sayfaları için özel methodlar
        if ($this->isMainEmlakPage($pageData['name'] ?? '')) {
            $specialMethods = ['search', 'export', 'bulkUpdate', 'filter'];
            $implementedSpecial = array_intersect($specialMethods, array_keys($methods));
            $score += count($implementedSpecial) * 0.3; // Özel methodlar bonus
        }

        // Context7 uyumlu methodlar
        $context7Methods = ['validateRequest', 'handleError', 'logActivity'];
        $implementedContext7 = array_intersect($context7Methods, array_keys($methods));
        $score += count($implementedContext7) * 0.2;

        // Check for Context7 compliance
        if (Str::contains($content, 'Context7')) {
            $score += 1;
        }

        return [
            'exists' => true,
            'methods' => array_keys($methods),
            'method_count' => count($methods),
            'issues' => $issues,
            'score' => max(0, min(10, $score))
        ];
    }

    protected function analyzeRoutes($pageName, $pageData = [])
    {
        $routeFile = base_path('routes/admin.php');
        if (!File::exists($routeFile)) {
            return ['exists' => false, 'score' => 0];
        }

        $content = File::get($routeFile);
        $score = 0;
        $routes = [];

        // If we have specific route info from discovery
        if (isset($pageData['route_name'])) {
            $routeName = $pageData['route_name'];
            if (Str::contains($content, $routeName)) {
                $score += 3;
                $routes[] = "Named route: {$routeName}";
            }
        }

        // Check for controller-based routes
        if (isset($pageData['route_prefix'])) {
            $prefix = $pageData['route_prefix'];
            $routePattern = '/Route::.*' . preg_quote($prefix, '/') . '/i';
            preg_match_all($routePattern, $content, $matches);

            $score += count($matches[0]);
            $routes = array_merge($routes, $matches[0]);
        }

        return [
            'route_count' => count($routes),
            'routes' => $routes,
            'has_resource_routes' => Str::contains($content, "Route::resource"),
            'has_middleware' => Str::contains($content, "middleware"),
            'score' => min(10, $score)
        ];
    }

    protected function analyzeView($pageName, $pageData = [])
    {
        // Check specific view if provided
        if (isset($pageData['view'])) {
            $viewPath = "resources/views/{$pageData['view']}.blade.php";
            if (File::exists(base_path($viewPath))) {
                return $this->analyzeSpecificView(base_path($viewPath));
            }
        }

        // Generate possible view paths
        $pageSlug = Str::slug($pageName);
        $viewPaths = [
            "resources/views/admin/{$pageSlug}/index.blade.php",
            "resources/views/admin/{$pageSlug}.blade.php",
            str_replace('-', '/', "resources/views/admin/{$pageSlug}/index.blade.php"),
        ];

        // Add route prefix based paths
        if (isset($pageData['route_prefix'])) {
            $prefix = $pageData['route_prefix'];
            $viewPaths[] = "resources/views/admin/{$prefix}/index.blade.php";
            $viewPaths[] = "resources/views/admin/{$prefix}.blade.php";
        }

        foreach ($viewPaths as $path) {
            if (File::exists(base_path($path))) {
                return $this->analyzeSpecificView(base_path($path));
            }
        }

        return [
            'exists' => false,
            'checked_paths' => $viewPaths,
            'score' => 0
        ];
    }

    protected function analyzeSpecificView($fullPath)
    {
        $content = File::get($fullPath);

        // 🎨 CSS SYSTEM COMPLIANCE CHECK
        $cssAnalysis = $this->analyzeCSSCompliance($content);

        // 🔍 INNOVATION DETECTION
        $innovationAnalysis = $this->detectInnovations($content);

        return [
            'exists' => true,
            'path' => str_replace(base_path() . '/', '', $fullPath),
            'size' => strlen($content),
            'uses_neo_layout' => Str::contains($content, 'admin.layouts.neo'),
            'uses_alpine' => Str::contains($content, 'x-data'),
            'has_scripts' => Str::contains($content, '@push(\'scripts\')'),
            'has_csrf' => Str::contains($content, '@csrf'),
            'uses_components' => Str::contains($content, '<x-'),
            'css_compliance' => $cssAnalysis,
            'innovations' => $innovationAnalysis,
            'score' => $this->calculateViewScore($content, $cssAnalysis, $innovationAnalysis)
        ];
    }

    protected function analyzeCSSCompliance($content)
    {
        $issues = [];
        $score = 10;

        // Neo Design System class kontrolü
        $neoClasses = ['neo-card', 'neo-btn', 'neo-btn-primary', 'neo-btn-outline', 'neo-grid', 'neo-container'];
        $legacyClasses = ['btn-', 'card-', 'bg-blue-', 'bg-green-', 'bg-red-'];

        $neoUsage = 0;
        $legacyUsage = 0;

        foreach ($neoClasses as $neoClass) {
            $neoUsage += substr_count($content, $neoClass);
        }

        foreach ($legacyClasses as $legacyClass) {
            $legacyUsage += substr_count($content, $legacyClass);
        }

        // CSS sistem uygunluk skorlaması
        if ($legacyUsage > 0) {
            $issues[] = "Legacy CSS classes found: {$legacyUsage} occurrences";
            $score -= min(3, $legacyUsage * 0.5);
        }

        if ($neoUsage === 0) {
            $issues[] = "No Neo Design System classes found";
            $score -= 2;
        } else {
            $score += min(2, $neoUsage * 0.1); // Neo kullanımı bonus
        }

        // Tailwind kullanımı kontrolü
        if (Str::contains($content, 'class=') && !Str::contains($content, 'neo-')) {
            $tailwindMatches = preg_match_all('/class=["\'][^"\']*["\']/', $content);
            if ($tailwindMatches > 10) {
                $issues[] = "Heavy Tailwind usage without Neo classes";
                $score -= 1;
            }
        }

        return [
            'score' => max(0, min(10, $score)),
            'issues' => $issues,
            'neo_usage' => $neoUsage,
            'legacy_usage' => $legacyUsage,
            'compliant' => $legacyUsage === 0 && $neoUsage > 0
        ];
    }

    protected function detectInnovations($content)
    {
        $innovations = [];
        $score = 0;

        // AI/ML Integration Detection
        if (Str::contains($content, ['ai-', 'ml-', 'artificial', 'intelligence', 'machine-learning'])) {
            $innovations[] = 'AI/ML Integration Detected';
            $score += 2;
        }

        // Real-time Features
        if (Str::contains($content, ['websocket', 'realtime', 'live-', 'socket.io', 'pusher'])) {
            $innovations[] = 'Real-time Features';
            $score += 1.5;
        }

        // Advanced UI Components
        if (Str::contains($content, ['x-data', 'alpine', '@click', '@submit', 'x-show', 'x-if'])) {
            $innovations[] = 'Alpine.js Reactive Components';
            $score += 1;
        }

        // Modern CSS Features
        if (Str::contains($content, ['grid-cols-', 'flex-', 'gradient-', 'backdrop-', 'animate-'])) {
            $innovations[] = 'Modern CSS Grid/Flexbox/Animations';
            $score += 0.5;
        }

        // API Integration
        if (Str::contains($content, ['fetch(', 'axios', 'api/', 'endpoints'])) {
            $innovations[] = 'Modern API Integration';
            $score += 1;
        }

        // Progressive Web App Features
        if (Str::contains($content, ['service-worker', 'manifest.json', 'offline'])) {
            $innovations[] = 'PWA Features';
            $score += 2;
        }

        // Context7 Compliance
        if (Str::contains($content, ['Context7', 'context7-', 'migration-'])) {
            $innovations[] = 'Context7 System Integration';
            $score += 1;
        }

        return [
            'detected' => $innovations,
            'count' => count($innovations),
            'score' => $score,
            'is_innovative' => count($innovations) > 2
        ];
    }

    protected function checkContext7Compliance($page)
    {
        $issues = [];
        $score = 10;

        // Context7 naming conventions
        $expectedPatterns = [
            'il_id',
            'ilce_id',
            'mahalle_id',
            'aktif_mi',
            'is_published',
            'created_at',
            'updated_at'
        ];

        return [
            'score' => $score,
            'issues' => $issues,
            'compliant' => count($issues) === 0
        ];
    }


    protected function calculateScore($analysis)
    {
        // 🧠 AKILLI SKORLAMA SİSTEMİ V2.0

        // Ana emlak sayfaları için özel ağırlıklar
        $isMainEmlakPage = $this->isMainEmlakPage($analysis['page']);
        $isAIPage = $this->isAIPage($analysis['page']);
        $isManagementPage = $this->isManagementPage($analysis['page']);

        // Dinamik ağırlık sistemi
        if ($isMainEmlakPage) {
            // İlan, Talep, Kişiler gibi ana sayfalar - daha katı değerlendirme
            $weights = [
                'controller_analysis' => 0.35, // Controller çok önemli
                'route_analysis' => 0.25,     // Route tanımları kritik
                'view_analysis' => 0.25,      // UI/UX önemli
                'context7_compliance' => 0.10, // Context7 kuralları
                'accessibility' => 0.05       // Erişilebilirlik
            ];
            $minimumScore = 6.0; // Ana sayfalar için minimum skor
        } elseif ($isAIPage) {
            // AI sayfaları - özel değerlendirme
            $weights = [
                'controller_analysis' => 0.40, // AI logic çok kritik
                'route_analysis' => 0.20,
                'view_analysis' => 0.20,
                'context7_compliance' => 0.15, // Context7 uyumu önemli
                'accessibility' => 0.05
            ];
            $minimumScore = 5.5;
        } elseif ($isManagementPage) {
            // Yönetim sayfaları - dengeli değerlendirme
            $weights = [
                'controller_analysis' => 0.30,
                'route_analysis' => 0.25,
                'view_analysis' => 0.25,
                'context7_compliance' => 0.15,
                'accessibility' => 0.05
            ];
            $minimumScore = 5.0;
        } else {
            // Genel sayfalar - standart ağırlık
            $weights = [
                'controller_analysis' => 0.30,
                'route_analysis' => 0.20,
                'view_analysis' => 0.20,
                'context7_compliance' => 0.15,
                'accessibility' => 0.10,
                'performance' => 0.03,
                'security' => 0.02
            ];
            $minimumScore = 4.0;
        }

        $totalScore = 0;
        foreach ($weights as $key => $weight) {
            if (isset($analysis[$key]['score'])) {
                $totalScore += $analysis[$key]['score'] * $weight;
            }
        }

        // Bonus puanlama sistemi
        $bonusScore = $this->calculateBonusPoints($analysis);
        $totalScore += $bonusScore;

        // Minimum skor kontrolü
        $finalScore = max($minimumScore, $totalScore);

        // Maksimum 10 puan sınırı
        return round(min(10.0, $finalScore), 1);
    }

    // 🎯 Ana emlak sayfalarını tespit et
    protected function isMainEmlakPage($pageName)
    {
        $mainPages = ['İlan Yönetimi', 'Talep Yönetimi', 'Kişiler Yönetimi', 'Danışman Yönetimi'];
        return Str::contains($pageName, $mainPages);
    }

    // 🤖 AI sayfalarını tespit et
    protected function isAIPage($pageName)
    {
        return Str::contains($pageName, ['AI', 'Akıllı', 'Yapay Zeka']);
    }

    // 📊 Yönetim sayfalarını tespit et
    protected function isManagementPage($pageName)
    {
        return Str::contains($pageName, ['Yönetimi', 'Management', 'Dashboard', 'Analiz']);
    }

    // ⭐ Bonus puan sistemi
    protected function calculateBonusPoints($analysis)
    {
        $bonus = 0;

        // Context7 tam uyumlu ise +0.5
        if (isset($analysis['context7_compliance']['compliant']) && $analysis['context7_compliance']['compliant']) {
            $bonus += 0.5;
        }

        // Route sayısı yüksekse +0.3
        if (isset($analysis['route_analysis']['route_count']) && $analysis['route_analysis']['route_count'] >= 5) {
            $bonus += 0.3;
        }

        // View dosyası varsa +0.2
        if (isset($analysis['view_analysis']['has_view']) && $analysis['view_analysis']['has_view']) {
            $bonus += 0.2;
        }

        return $bonus;
    }

    protected function calculateViewScore($content, $cssAnalysis = null, $innovationAnalysis = null)
    {
        $score = 0;

        // Temel skorlama
        if (Str::contains($content, 'admin.layouts.neo')) $score += 2;
        if (Str::contains($content, 'x-data')) $score += 2;
        if (Str::contains($content, '@csrf')) $score += 1;
        if (Str::contains($content, '<x-')) $score += 2;
        if (Str::contains($content, '@push(\'scripts\')')) $score += 1;
        if (strlen($content) > 1000) $score += 2; // Has substantial content

        // CSS compliance bonus/penalty
        if ($cssAnalysis) {
            $score += $cssAnalysis['score'] * 0.2; // CSS skorunun %20'si
            if ($cssAnalysis['compliant']) {
                $score += 1; // Neo compliance bonus
            }
        }

        // Innovation bonus
        if ($innovationAnalysis) {
            $score += $innovationAnalysis['score'] * 0.15; // Innovation skorunun %15'i
        }

        return min(10, $score);
    }

    protected function extractMethods($content)
    {
        preg_match_all('/public\s+function\s+(\w+)\s*\(/', $content, $matches);

        $methods = [];
        foreach ($matches[1] as $method) {
            $methods[$method] = true;
        }

        return $methods;
    }

    // 🎯 Sayfa tipine göre gerekli methodları belirle
    protected function getRequiredMethodsForPage($pageName)
    {
        // Ana emlak sayfaları - kapsamlı CRUD gerekli
        if ($this->isMainEmlakPage($pageName)) {
            return ['index', 'show', 'create', 'store', 'edit', 'update', 'destroy', 'search'];
        }

        // AI sayfaları - özel AI methodları
        if ($this->isAIPage($pageName)) {
            return ['index', 'chat', 'analyze', 'suggest'];
        }

        // Yönetim sayfaları - temel CRUD
        if ($this->isManagementPage($pageName)) {
            return ['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'];
        }

        // Genel sayfalar - minimum gereksinim
        return ['index', 'show'];
    }

    // ⚠️ Method öncelik seviyesi belirle
    protected function getMethodPriority($method, $pageName)
    {
        $criticalMethods = ['index', 'show'];
        $importantMethods = ['create', 'store', 'edit', 'update'];
        $optionalMethods = ['destroy', 'search', 'export'];

        if (in_array($method, $criticalMethods)) {
            return 'CRITICAL';
        } elseif (in_array($method, $importantMethods)) {
            return 'IMPORTANT';
        } else {
            return 'OPTIONAL';
        }
    }

    protected function generateReport()
    {
        $format = $this->option('format');
        $outputFile = $this->option('output');

        switch ($format) {
            case 'json':
                $report = $this->generateJsonReport();
                break;
            case 'html':
                $report = $this->generateHtmlReport();
                break;
            default:
                $this->generateConsoleReport();
                return;
        }

        if ($outputFile) {
            File::put($outputFile, $report);
            $this->info("Report saved to: {$outputFile}");
        } else {
            $this->line($report);
        }
    }

    protected function generateConsoleReport()
    {
        $this->newLine();
        $this->info('📊 EmlakPro Complete Page Analysis Report');
        $this->info('==================================================');
        $this->newLine();

        // Group results by category first
        $categorized = [];
        foreach ($this->results as $pageKey => $analysis) {
            $category = $this->getPageCategory($analysis['page'], $analysis['controller'] ?? '');
            if (!isset($categorized[$category])) {
                $categorized[$category] = [];
            }
            $categorized[$category][$pageKey] = $analysis;
        }

        // Display results by category
        foreach ($categorized as $categoryName => $pages) {
            $this->info("=== {$categoryName} ===");

            // Sort pages by score (highest first)
            uasort($pages, function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            $categoryIndex = 1;
            foreach ($pages as $pageKey => $analysis) {
                $scoreColor = $this->getScoreColor($analysis['score']);
                $issueText = isset($analysis['controller_analysis']['issues'][0]) ? $analysis['controller_analysis']['issues'][0] : 'OK';
                $this->line("{$categoryIndex}. {$analysis['page']} [{$analysis['score']}/10] {$issueText}");
                $categoryIndex++;
            }
            $this->newLine();
        }

        // Group results by severity for summary
        $grouped = [
            'critical' => [],
            'danger' => [],
            'warning' => [],
            'success' => []
        ];

        foreach ($this->results as $pageKey => $analysis) {
            $grouped[$analysis['severity']][$pageKey] = $analysis;
        }

        // Show critical issues
        if (!empty($grouped['critical'])) {
            $this->error('🔴 CRITICAL ISSUES (' . count($grouped['critical']) . ')');
            foreach ($grouped['critical'] as $pageKey => $analysis) {
                $this->line("  - {$analysis['page']}: Score {$analysis['score']}/10");
                foreach ($analysis['controller_analysis']['issues'] ?? [] as $issue) {
                    $this->line("    • {$issue}");
                }
            }
            $this->newLine();
        }

        // Show danger level issues
        if (!empty($grouped['danger'])) {
            $this->warn('🟠 HIGH PRIORITY (' . count($grouped['danger']) . ')');
            foreach ($grouped['danger'] as $pageKey => $analysis) {
                $this->line("  - {$analysis['page']}: Score {$analysis['score']}/10");
            }
            $this->newLine();
        }

        // Show warning level issues
        if (!empty($grouped['warning'])) {
            $this->comment('🟡 MEDIUM PRIORITY (' . count($grouped['warning']) . ')');
            foreach ($grouped['warning'] as $pageKey => $analysis) {
                $this->line("  - {$analysis['page']}: Score {$analysis['score']}/10");
            }
            $this->newLine();
        }

        // Show successful pages
        if (!empty($grouped['success'])) {
            $this->info('✅ WELL-IMPLEMENTED (' . count($grouped['success']) . ')');
            foreach ($grouped['success'] as $pageKey => $analysis) {
                $this->line("  - {$analysis['page']}: Score {$analysis['score']}/10");
            }
            $this->newLine();
        }

        // Overall statistics
        $this->info('📈 OVERALL STATISTICS');
        $avgScore = count($this->scores) > 0 ? round(array_sum($this->scores) / count($this->scores), 1) : 0;
        $this->line("  Average Score: {$avgScore}/10");
        $this->line("  Total Pages Analyzed: " . count($this->results));
        $this->line("  Critical Issues: " . count($grouped['critical']));
        $this->line("  Warning Issues: " . count($grouped['warning']));
        $this->newLine();

        // Generate recommendations
        $this->generateRecommendations();
    }

    protected function generateRecommendations()
    {
        $this->info('💡 RECOMMENDATIONS');
        $recommendations = [
            '1. Implement missing controllers (Priority: Critical)',
            '2. Add proper error handling and validation',
            '3. Ensure Context7 compliance in all files',
            '4. Add comprehensive testing coverage',
            '5. Implement real-time monitoring'
        ];

        foreach ($recommendations as $rec) {
            $this->line($rec);
        }
    }

    protected function generateJsonReport()
    {
        return json_encode([
            'results' => $this->results,
            'summary' => [
                'total_pages' => count($this->results),
                'average_score' => count($this->scores) > 0 ? round(array_sum($this->scores) / count($this->scores), 1) : 0
            ]
        ], JSON_PRETTY_PRINT);
    }

    protected function generateHtmlReport()
    {
        return '<h1>EmlakPro Page Analysis Report</h1><p>Total Pages: ' . count($this->results) . '</p>';
    }

    protected function saveAnalysisToJSON()
    {
        // Create analysis directory if it doesn't exist
        $analysisDir = storage_path('app/analysis');
        if (!File::exists($analysisDir)) {
            File::makeDirectory($analysisDir, 0755, true);
        }

        // Prepare data for JSON export
        $analysisData = [
            'generated_at' => now()->toISOString(),
            'total_pages' => count($this->results),
            'average_score' => count($this->scores) > 0 ? round(array_sum($this->scores) / count($this->scores), 1) : 0,
            'results' => $this->results,
            'statistics' => [
                'critical' => collect($this->results)->where('severity', 'critical')->count(),
                'danger' => collect($this->results)->where('severity', 'danger')->count(),
                'warning' => collect($this->results)->where('severity', 'warning')->count(),
                'success' => collect($this->results)->where('severity', 'success')->count(),
            ],
            'css_compliance_stats' => $this->calculateCSSComplianceStats(),
            'innovation_stats' => $this->calculateInnovationStats()
        ];

        // Save to JSON file
        $jsonFile = $analysisDir . '/complete_pages_analysis.json';
        File::put($jsonFile, json_encode($analysisData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Analysis data saved to: {$jsonFile}");
    }

    protected function calculateCSSComplianceStats()
    {
        $totalPages = 0;
        $compliantPages = 0;
        $neoUsageTotal = 0;
        $legacyUsageTotal = 0;

        foreach ($this->results as $result) {
            if (isset($result['view_analysis']['css_compliance'])) {
                $totalPages++;
                $css = $result['view_analysis']['css_compliance'];

                if ($css['compliant'] ?? false) {
                    $compliantPages++;
                }

                $neoUsageTotal += $css['neo_usage'] ?? 0;
                $legacyUsageTotal += $css['legacy_usage'] ?? 0;
            }
        }

        return [
            'total_pages_analyzed' => $totalPages,
            'compliant_pages' => $compliantPages,
            'compliance_rate' => $totalPages > 0 ? round(($compliantPages / $totalPages) * 100, 1) : 0,
            'neo_usage_total' => $neoUsageTotal,
            'legacy_usage_total' => $legacyUsageTotal
        ];
    }

    protected function calculateInnovationStats()
    {
        $totalPages = 0;
        $innovativePages = 0;
        $totalInnovations = 0;
        $innovationTypes = [];

        foreach ($this->results as $result) {
            if (isset($result['view_analysis']['innovations'])) {
                $totalPages++;
                $innovations = $result['view_analysis']['innovations'];

                if ($innovations['is_innovative'] ?? false) {
                    $innovativePages++;
                }

                $totalInnovations += $innovations['count'] ?? 0;

                // Count innovation types
                foreach ($innovations['detected'] ?? [] as $innovation) {
                    $innovationTypes[$innovation] = ($innovationTypes[$innovation] ?? 0) + 1;
                }
            }
        }

        return [
            'total_pages_analyzed' => $totalPages,
            'innovative_pages' => $innovativePages,
            'innovation_rate' => $totalPages > 0 ? round(($innovativePages / $totalPages) * 100, 1) : 0,
            'total_innovations_detected' => $totalInnovations,
            'innovation_types' => $innovationTypes
        ];
    }
}
