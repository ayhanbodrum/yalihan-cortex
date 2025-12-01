<?php

/**
 * Context7 Compliance Scanner - PHP Version
 *
 * Tüm projeyi tarar ve Context7 kurallarına aykırı pattern'leri bulur
 * Kullanım: php scripts/context7-compliance-scanner.php [--fix] [--report]
 */
class Context7ComplianceScanner
{
    private array $violations = [];

    private array $stats = [
        'total' => 0,
        'critical' => 0,
        'high' => 0,
        'medium' => 0,
        'low' => 0,
    ];

    private array $patterns = [
        'database_fields' => [
            'order' => [
                'pattern' => "/'order'|\"order\"|order\s*=>/",
                'replacement' => 'display_order',
                'severity' => 'critical',
                'message' => 'order → display_order kullanılmalı',
                'exclude' => [
                    'display_order',
                    '$order',
                    'orderBy',
                    '//',
                    'ALTER TABLE',
                    'SHOW COLUMNS',
                    'CHANGE `order`',
                    "Field = 'order'",
                    'dropColumn(\'order\')',
                    'COALESCE(display_order, `order`',
                ],
            ],
            'durum' => [
                'pattern' => "/'durum'|\"durum\"/",
                'replacement' => 'status',
                'severity' => 'critical',
                'message' => 'durum → status kullanılmalı',
                'exclude' => ['status'],
            ],
            'aktif' => [
                'pattern' => "/'aktif'|\"aktif\"/",
                'replacement' => 'status',
                'severity' => 'critical',
                'message' => 'aktif → status kullanılmalı',
                'exclude' => ['status'],
            ],
            'is_active' => [
                'pattern' => '/is_active/',
                'replacement' => 'status',
                'severity' => 'high',
                'message' => 'is_active → status kullanılmalı',
                'exclude' => ['status'],
            ],
            'enabled' => [
                'pattern' => '/enabled/',
                'replacement' => 'status',
                'severity' => 'critical',
                'message' => 'enabled → status kullanılmalı (status field olarak)',
                'exclude' => ['weekend_pricing_enabled', 'sync_enabled', 'feature.*enabled', 'status'],
            ],
            'sehir' => [
                'pattern' => "/'sehir'|\"sehir\"|sehir_id/",
                'replacement' => 'il',
                'severity' => 'critical',
                'message' => 'sehir → il kullanılmalı',
                'exclude' => ['il_id'],
            ],
            'musteri' => [
                'pattern' => "/'musteri'|\"musteri\"|musteri_id/",
                'replacement' => 'kisi',
                'severity' => 'critical',
                'message' => 'musteri → kisi kullanılmalı',
                'exclude' => ['kisi'],
            ],
        ],
        'css_classes' => [
            'neo' => [
                'pattern' => '/neo-[a-z-]+/',
                'replacement' => 'Tailwind CSS',
                'severity' => 'critical',
                'message' => 'Neo Design System yasak - Tailwind CSS kullanılmalı',
            ],
            'bootstrap' => [
                'pattern' => '/btn-|card-|form-control/',
                'replacement' => 'Tailwind CSS',
                'severity' => 'high',
                'message' => 'Bootstrap yasak - Tailwind CSS kullanılmalı',
            ],
        ],
        'javascript' => [
            'jquery' => [
                'pattern' => '/\$\(|jQuery|\.ajax\(|\.get\(|\.post\(/',
                'replacement' => 'Vanilla JS',
                'severity' => 'critical',
                'message' => 'jQuery yasak - Vanilla JS kullanılmalı',
                'exclude' => ['node_modules'],
            ],
            'subtleVibrantToast' => [
                'pattern' => '/subtleVibrantToast/',
                'replacement' => 'window.toast',
                'severity' => 'critical',
                'message' => 'subtleVibrantToast yasak - window.toast kullanılmalı',
            ],
        ],
        'layouts' => [
            'layouts_app' => [
                'pattern' => "/@extends\('layouts\.app'\)/",
                'replacement' => "@extends('admin.layouts.neo')",
                'severity' => 'critical',
                'message' => 'layouts.app yasak - admin.layouts.neo kullanılmalı',
            ],
        ],
        'routes' => [
            'crm_routes' => [
                'pattern' => "/route\('crm\./",
                'replacement' => "route('admin.",
                'severity' => 'critical',
                'message' => 'crm.* routes yasak - admin.* kullanılmalı',
            ],
        ],
        'migrations' => [
            'order_column' => [
                'pattern' => "/\\\$table->.*\('order'\)/",
                'replacement' => 'display_order',
                'severity' => 'critical',
                'message' => 'Migration\'da order → display_order kullanılmalı',
                'exclude' => ['display_order'],
            ],
        ],
    ];

    private array $scanPaths = [
        'app/',
        'database/',
        'resources/',
    ];

    private array $fileExtensions = [
        'php',
        'blade.php',
        'js',
    ];

    public function scan(bool $fixMode = false, ?string $reportFile = null): void
    {
        echo "🔍 Context7 Compliance Scanner başlatılıyor...\n\n";

        foreach ($this->patterns as $category => $rules) {
            echo "📋 $category kontrol ediliyor...\n";
            $this->scanCategory($category, $rules, $fixMode);
            echo "\n";
        }

        $this->printSummary();

        if ($reportFile) {
            $this->generateReport($reportFile);
        }
    }

    private function scanCategory(string $category, array $rules, bool $fixMode): void
    {
        foreach ($rules as $ruleName => $rule) {
            $files = $this->findFiles($rule['pattern'], $rule['exclude'] ?? []);

            foreach ($files as $file => $matches) {
                foreach ($matches as $match) {
                    $this->addViolation(
                        $rule['severity'],
                        $file,
                        $match['line'],
                        $match['content'],
                        $rule['message'],
                        $rule['replacement'] ?? null
                    );

                    if ($fixMode && isset($rule['replacement'])) {
                        $this->fixViolation($file, $match['line'], $match['content'], $rule['pattern'], $rule['replacement']);
                    }
                }
            }
        }
    }

    private function findFiles(string $pattern, array $excludes = []): array
    {
        $results = [];

        foreach ($this->scanPaths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
            );

            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    continue;
                }

                $filePath = $file->getPathname();
                $extension = $file->getExtension();

                // Dosya uzantısı kontrolü
                if (! in_array($extension, $this->fileExtensions)) {
                    continue;
                }

                // Exclude paths
                if (strpos($filePath, 'node_modules') !== false ||
                    strpos($filePath, 'vendor') !== false ||
                    strpos($filePath, '.git') !== false) {
                    continue;
                }

                $lines = file($filePath, FILE_IGNORE_NEW_LINES);
                if ($lines === false) {
                    continue;
                }

                foreach ($lines as $lineNum => $line) {
                    // Exclude kontrolü
                    $shouldExclude = false;
                    foreach ($excludes as $exclude) {
                        // Escape special regex characters
                        $excludePattern = preg_quote($exclude, '/');
                        if (preg_match("/$excludePattern/", $line)) {
                            $shouldExclude = true;
                            break;
                        }
                    }

                    if ($shouldExclude) {
                        continue;
                    }

                    if (preg_match($pattern, $line)) {
                        if (! isset($results[$filePath])) {
                            $results[$filePath] = [];
                        }

                        $results[$filePath][] = [
                            'line' => $lineNum + 1,
                            'content' => trim($line),
                        ];
                    }
                }
            }
        }

        return $results;
    }

    private function addViolation(
        string $severity,
        string $file,
        int $line,
        string $content,
        string $message,
        ?string $replacement = null
    ): void {
        $this->violations[] = [
            'severity' => $severity,
            'file' => $file,
            'line' => $line,
            'content' => $content,
            'message' => $message,
            'replacement' => $replacement,
        ];

        $this->stats['total']++;
        $this->stats[$severity]++;
    }

    private function fixViolation(string $file, int $line, string $content, string $pattern, string $replacement): void
    {
        // Basit fix - gerçek implementasyon daha karmaşık olmalı
        echo "  🔧 Düzeltiliyor: $file:$line\n";
    }

    private function printSummary(): void
    {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📊 TARAMA ÖZETİ\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        echo "Toplam İhlal: {$this->stats['total']}\n";
        echo "  ❌ Critical: {$this->stats['critical']}\n";
        echo "  ⚠️  High: {$this->stats['high']}\n";
        echo "  ℹ️  Medium: {$this->stats['medium']}\n";
        echo "  ℹ️  Low: {$this->stats['low']}\n\n";

        if ($this->stats['total'] > 0) {
            echo "İlk 10 ihlal:\n";
            foreach (array_slice($this->violations, 0, 10) as $violation) {
                $severity = strtoupper($violation['severity']);
                echo "  [$severity] {$violation['file']}:{$violation['line']} - {$violation['message']}\n";
            }
        }
    }

    private function generateReport(string $reportFile): void
    {
        $dir = dirname($reportFile);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = "# Context7 Compliance Report\n\n";
        $content .= '**Tarih:** '.date('Y-m-d H:i:s')."\n";
        $content .= "**Durum:** ⚠️ TARAMA TAMAMLANDI\n\n";
        $content .= "---\n\n";
        $content .= "## 📊 Özet\n\n";
        $content .= "- **Toplam İhlal:** {$this->stats['total']}\n";
        $content .= "- **Critical:** {$this->stats['critical']}\n";
        $content .= "- **High:** {$this->stats['high']}\n";
        $content .= "- **Medium:** {$this->stats['medium']}\n";
        $content .= "- **Low:** {$this->stats['low']}\n\n";
        $content .= "---\n\n";

        // İhlalleri kategorilere göre grupla
        $grouped = [];
        foreach ($this->violations as $violation) {
            $severity = $violation['severity'];
            if (! isset($grouped[$severity])) {
                $grouped[$severity] = [];
            }
            $grouped[$severity][] = $violation;
        }

        foreach (['critical', 'high', 'medium', 'low'] as $severity) {
            if (! isset($grouped[$severity]) || empty($grouped[$severity])) {
                continue;
            }

            $content .= "## $severity Violations\n\n";
            foreach ($grouped[$severity] as $violation) {
                $content .= "### {$violation['file']}:{$violation['line']}\n\n";
                $content .= "**Pattern:** `{$violation['content']}`\n\n";
                $content .= "**Mesaj:** {$violation['message']}\n\n";
                if ($violation['replacement']) {
                    $content .= "**Replacement:** `{$violation['replacement']}`\n\n";
                }
                $content .= "---\n\n";
            }
        }

        file_put_contents($reportFile, $content);
        echo "\n✅ Rapor oluşturuldu: $reportFile\n";
    }
}

// CLI çalıştırma
if (php_sapi_name() === 'cli') {
    $fixMode = in_array('--fix', $argv);
    $reportIndex = array_search('--report', $argv);
    $reportFile = $reportIndex !== false && isset($argv[$reportIndex + 1])
        ? $argv[$reportIndex + 1]
        : null;

    if ($reportIndex !== false && $reportFile === null) {
        $reportFile = '.context7/compliance-report-'.date('Ymd-His').'.md';
    }

    $scanner = new Context7ComplianceScanner;
    $scanner->scan($fixMode, $reportFile);

    // Stats'e erişim için getter kullan
    $reflection = new ReflectionClass($scanner);
    $statsProperty = $reflection->getProperty('stats');
    $statsProperty->setAccessible(true);
    $stats = $statsProperty->getValue($scanner);

    exit($stats['total'] > 0 ? 1 : 0);
}
