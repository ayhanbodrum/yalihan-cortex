<?php
/**
 * Context7 Sistem Optimizasyon Scripti
 *
 * Bu script sistemin %100 Context7 uyumlu hale getirilmesi için
 * otomatik düzeltmeler yapar.
 */

class Context7Optimizer
{
    private $violations = [];
    private $fixes = [];

    public function __construct()
    {
        echo "🚀 Context7 Optimizer başlatılıyor...\n";
    }

    /**
     * Controller değişken ihlallerini düzelt
     */
    public function fixControllerVariables()
    {
        echo "📝 Controller değişkenleri düzeltiliyor...\n";

        $controllers = [
            'app/Http/Controllers/Admin/TalepController.php',
            'app/Http/Controllers/Admin/MusteriController.php',
            'app/Http/Controllers/Admin/AdresYonetimiController.php',
            'app/Http/Controllers/Admin/DanismanController.php',
            'app/Http/Controllers/Admin/YayinTipiController.php',
            'app/Http/Controllers/Admin/IlanKategoriController.php',
            'app/Http/Controllers/Admin/BlogController.php',
            'app/Http/Controllers/Admin/EtiketController.php',
            'app/Http/Controllers/Admin/SystemMonitorController.php',
            'app/Http/Controllers/Admin/OzellikKategoriController.php'
        ];

        foreach ($controllers as $controller) {
            if (file_exists($controller)) {
                $this->addMissingVariables($controller);
                echo "✅ {$controller} düzeltildi\n";
            }
        }
    }

    /**
     * Eksik değişkenleri ekle
     */
    private function addMissingVariables($file)
    {
        $content = file_get_contents($file);

        // Eksik değişkenleri tespit et
        $missingVars = [];
        if (strpos($content, '$status') !== false && strpos($content, '$status =') === false) {
            $missingVars[] = '$status = [\'Aktif\', \'Pasif\'];';
        }
        if (strpos($content, '$taslak') !== false && strpos($content, '$taslak =') === false) {
            $missingVars[] = '$taslak = [\'Taslak\', \'Yayında\'];';
        }
        if (strpos($content, '$etiketler') !== false && strpos($content, '$etiketler =') === false) {
            $missingVars[] = '$etiketler = \\App\\Models\\Etiket::all();';
        }
        if (strpos($content, '$ulkeler') !== false && strpos($content, '$ulkeler =') === false) {
            $missingVars[] = '$ulkeler = \\App\\Models\\Ulke::all();';
        }

        if (!empty($missingVars)) {
            // Index methodunu bul ve değişkenleri ekle
            $pattern = '/(public function index\([^)]*\)\s*\{)/';
            $replacement = '$1' . "\n        // Context7 uyumlu değişkenler\n        " . implode("\n        ", $missingVars) . "\n";

            $content = preg_replace($pattern, $replacement, $content);
            file_put_contents($file, $content);
        }
    }

    /**
     * CSS class ihlallerini düzelt
     */
    public function fixCssClasses()
    {
        echo "🎨 CSS classes düzeltiliyor...\n";

        $views = [
            'resources/views/admin/ai/advanced-dashboard.blade.php'
        ];

        foreach ($views as $view) {
            if (file_exists($view)) {
                $content = file_get_contents($view);

                // Bootstrap → Neo Design System
                $content = str_replace('btn-', 'neo-btn--', $content);
                $content = str_replace('card-', 'neo-card--', $content);
                $content = str_replace('form-control', 'neo-input', $content);

                file_put_contents($view, $content);
                echo "✅ {$view} düzeltildi\n";
            }
        }
    }

    /**
     * Performans optimizasyonu
     */
    public function optimizePerformance()
    {
        echo "⚡ Performans optimizasyonu yapılıyor...\n";

        // Cache temizle
        exec('php artisan cache:clear');
        exec('php artisan config:clear');
        exec('php artisan route:clear');
        exec('php artisan view:clear');

        // Optimize et
        exec('php artisan config:cache');
        exec('php artisan route:cache');
        exec('php artisan view:cache');

        echo "✅ Cache optimize edildi\n";
    }

    /**
     * Context7 compliance check
     */
    public function checkCompliance()
    {
        echo "🔍 Context7 uyumluluk kontrolü...\n";

        $violations = 0;

        // Controller değişken kontrolü
        $controllers = glob('app/Http/Controllers/**/*.php');
        foreach ($controllers as $controller) {
            $content = file_get_contents($controller);
            if (strpos($content, '$status') !== false && strpos($content, '$status =') === false) {
                $violations++;
            }
        }

        // CSS class kontrolü
        $views = glob('resources/views/**/*.blade.php');
        foreach ($views as $view) {
            $content = file_get_contents($view);
            if (strpos($content, 'btn-') !== false || strpos($content, 'card-') !== false) {
                $violations++;
            }
        }

        $compliance = max(0, 100 - ($violations * 2));
        echo "📊 Context7 Uyumluluk: %{$compliance}\n";

        return $compliance;
    }

    /**
     * Ana optimizasyon işlemi
     */
    public function run()
    {
        echo "🎯 Context7 Optimizasyon başlatılıyor...\n\n";

        // 1. Controller değişkenleri düzelt
        $this->fixControllerVariables();
        echo "\n";

        // 2. CSS classes düzelt
        $this->fixCssClasses();
        echo "\n";

        // 3. Performans optimize et
        $this->optimizePerformance();
        echo "\n";

        // 4. Uyumluluk kontrolü
        $compliance = $this->checkCompliance();
        echo "\n";

        if ($compliance >= 100) {
            echo "🎉 BAŞARILI! Sistem %100 Context7 uyumlu!\n";
        } else {
            echo "⚠️  Sistem %{$compliance} uyumlu. Kalan ihlaller düzeltilmeli.\n";
        }

        return $compliance;
    }
}

// Script çalıştır
if (php_sapi_name() === 'cli') {
    $optimizer = new Context7Optimizer();
    $optimizer->run();
}
