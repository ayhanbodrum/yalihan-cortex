<?php

/**
 * Context7 Final Compliance Checker & Report Generator
 *
 * This script provides a comprehensive analysis of the current Context7 compliance
 * status after all transformations and optimizations.
 */

class Context7FinalComplianceChecker
{
    private $baseDir;
    private $forbiddenFields = ['status', 'is_active', 'aktif', 'il'];
    private $violations = [];
    private $stats = [];

    // Critical directories to check
    private $directories = [
        'app/Models',
        'app/Http/Controllers',
        'database/migrations',
        'database/seeders',
        'resources/views',
        'resources/js',
        'config',
        'routes'
    ];

    // Directories to exclude from compliance check
    private $excludeDirectories = [
        'scripts',
        'tests',
        '.context7',
        'backup',
        'backups'
    ];

    public function __construct($baseDir)
    {
        $this->baseDir = rtrim($baseDir, '/');
    }

    public function runFullComplianceCheck()
    {
        echo "\n🔍 Context7 Final Compliance Check - " . date('Y-m-d H:i:s') . "\n";
        echo str_repeat("=", 70) . "\n";

        $this->initializeStats();

        foreach ($this->directories as $directory) {
            $fullPath = $this->baseDir . '/' . $directory;
            if (is_dir($fullPath)) {
                echo "\n📁 Analyzing: $directory\n";
                $this->scanDirectory($fullPath, $directory);
            }
        }

        $this->generateComprehensiveReport();
        return $this->violations;
    }

    private function initializeStats()
    {
        foreach ($this->forbiddenFields as $field) {
            $this->stats[$field] = [
                'count' => 0,
                'files' => [],
                'categories' => []
            ];
        }
        $this->stats['total'] = 0;
        $this->stats['files_scanned'] = 0;
        $this->stats['clean_files'] = 0;
    }

    private function scanDirectory($dir, $relativePath)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                // Skip excluded directories
                $filePath = $file->getPathname();
                $shouldExclude = false;
                foreach ($this->excludeDirectories as $excludeDir) {
                    if (strpos($filePath, $excludeDir) !== false) {
                        $shouldExclude = true;
                        break;
                    }
                }

                if (!$shouldExclude) {
                    $this->stats['files_scanned']++;
                    $this->scanFile($file->getPathname(), $relativePath);
                }
            }
        }
    }

    private function scanFile($filePath, $baseDir)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $allowedExtensions = ['php', 'js', 'vue', 'blade.php', 'json', 'yaml', 'yml'];

        if (!in_array($extension, $allowedExtensions) &&
            !str_contains($filePath, '.blade.php')) {
            return;
        }

        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        $fileViolations = [];

        foreach ($lines as $lineNumber => $line) {
            foreach ($this->forbiddenFields as $field) {
                if ($this->containsViolation($line, $field, $filePath)) {
                    $violation = [
                        'field' => $field,
                        'file' => str_replace($this->baseDir . '/', '', $filePath),
                        'line' => $lineNumber + 1,
                        'content' => trim($line),
                        'category' => $this->categorizeViolation($line, $field, $baseDir)
                    ];

                    $fileViolations[] = $violation;
                    $this->violations[] = $violation;
                    $this->stats[$field]['count']++;
                    $this->stats['total']++;

                    if (!in_array($violation['file'], $this->stats[$field]['files'])) {
                        $this->stats[$field]['files'][] = $violation['file'];
                    }

                    $category = $violation['category'];
                    if (!isset($this->stats[$field]['categories'][$category])) {
                        $this->stats[$field]['categories'][$category] = 0;
                    }
                    $this->stats[$field]['categories'][$category]++;
                }
            }
        }

        if (empty($fileViolations)) {
            $this->stats['clean_files']++;
        }
    }

    private function containsViolation($line, $field, $filePath)
    {
        // Skip comments and strings that are clearly documentation
        if (preg_match('/^\s*[\/\*#]/', $line) ||
            preg_match('/^\s*\*/', $line)) {
            return false;
        }

        // Special handling for compound fields that are legitimate
        if ($field === 'status') {
            $legitimateCompounds = [
                'imar_durumu', 'tapu_durumu', 'kredi_durumu',
                'yapim_durumu', 'satis_durumu', 'kira_durumu',
                'proje_durumu', 'yapisal_durumu'
            ];

            foreach ($legitimateCompounds as $compound) {
                if (stripos($line, $compound) !== false) {
                    return false;
                }
            }
        }

        // Check for field usage patterns
        $patterns = [
            '/\b' . preg_quote($field, '/') . '\b/',
            '/["\']' . preg_quote($field, '/') . '["\']/',
            '/' . preg_quote($field, '/') . '\s*[:=]/',
            '/\$' . preg_quote($field, '/') . '\b/',
            '/\.' . preg_quote($field, '/') . '\b/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }

        return false;
    }

    private function categorizeViolation($line, $field, $baseDir)
    {
        // Database-related
        if (strpos($baseDir, 'migrations') !== false) {
            if (preg_match('/Schema::/', $line)) return 'migration_schema';
            if (preg_match('/index|Index/', $line)) return 'migration_index';
            return 'migration_other';
        }

        if (strpos($baseDir, 'seeders') !== false) {
            return 'seeder';
        }

        // Model-related
        if (strpos($baseDir, 'Models') !== false) {
            if (preg_match('/fillable|guarded/', $line)) return 'model_fillable';
            if (preg_match('/casts/', $line)) return 'model_casts';
            if (preg_match('/function|method/', $line)) return 'model_method';
            return 'model_other';
        }

        // Controller-related
        if (strpos($baseDir, 'Controllers') !== false) {
            if (preg_match('/where|orWhere/', $line)) return 'controller_query';
            if (preg_match('/request|Request/', $line)) return 'controller_request';
            return 'controller_other';
        }

        // View-related
        if (strpos($baseDir, 'views') !== false) {
            if (preg_match('/\{\{|\@/', $line)) return 'blade_template';
            return 'view_other';
        }

        // JavaScript-related
        if (strpos($baseDir, 'js') !== false || strpos($line, '.js') !== false) {
            return 'javascript';
        }

        return 'other';
    }

    private function generateComprehensiveReport()
    {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "📊 FINAL CONTEXT7 COMPLIANCE REPORT\n";
        echo str_repeat("=", 70) . "\n";

        // Overall Statistics
        echo "\n🏆 OVERALL STATISTICS:\n";
        echo "├── Total Violations: " . $this->stats['total'] . "\n";
        echo "├── Files Scanned: " . $this->stats['files_scanned'] . "\n";
        echo "├── Clean Files: " . $this->stats['clean_files'] . "\n";
        $complianceRate = round(($this->stats['clean_files'] / $this->stats['files_scanned']) * 100, 2);
        echo "└── Compliance Rate: {$complianceRate}%\n";

        // Violations by Field
        echo "\n🎯 VIOLATIONS BY FIELD:\n";
        foreach ($this->forbiddenFields as $field) {
            $count = $this->stats[$field]['count'];
            $fileCount = count($this->stats[$field]['files']);
            echo "├── '$field': $count violations in $fileCount files\n";

            if (!empty($this->stats[$field]['categories'])) {
                arsort($this->stats[$field]['categories']);
                $topCategories = array_slice($this->stats[$field]['categories'], 0, 3, true);
                foreach ($topCategories as $category => $categoryCount) {
                    echo "│   ├── $category: $categoryCount\n";
                }
            }
        }

        // Priority Actions
        echo "\n🚀 PRIORITY ACTIONS:\n";
        $this->generatePriorityActions();

        // Progress Tracking
        echo "\n📈 PROGRESS TRACKING:\n";
        echo "├── Started with: ~1,729 violations\n";
        echo "├── Current total: " . $this->stats['total'] . " violations\n";
        $reductionPercent = round((1 - ($this->stats['total'] / 1729)) * 100, 2);
        echo "├── Total reduction: {$reductionPercent}%\n";
        echo "└── Remaining work: " . $this->stats['total'] . " violations to resolve\n";

        // Category Breakdown
        echo "\n📋 DETAILED BREAKDOWN BY CATEGORY:\n";
        $this->generateCategoryBreakdown();
    }

    private function generatePriorityActions()
    {
        $priorities = [];

        foreach ($this->stats as $field => $data) {
            if ($field === 'total' || $field === 'files_scanned' || $field === 'clean_files') {
                continue;
            }

            if (!empty($data['categories'])) {
                foreach ($data['categories'] as $category => $count) {
                    $priority = $this->calculatePriority($category, $count);
                    $priorities[] = [
                        'field' => $field,
                        'category' => $category,
                        'count' => $count,
                        'priority' => $priority,
                        'action' => $this->suggestAction($category, $field)
                    ];
                }
            }
        }

        // Sort by priority (higher is more important)
        usort($priorities, function($a, $b) {
            return $b['priority'] - $a['priority'];
        });

        $topPriorities = array_slice($priorities, 0, 10);
        foreach ($topPriorities as $item) {
            echo "├── Priority {$item['priority']}: {$item['field']} in {$item['category']} ({$item['count']} items)\n";
            echo "│   └── Action: {$item['action']}\n";
        }
    }

    private function calculatePriority($category, $count)
    {
        $basePriority = 5;

        // High priority categories
        $highPriorityCategories = [
            'migration_schema' => 10,
            'migration_index' => 9,
            'model_fillable' => 8,
            'controller_query' => 7,
            'blade_template' => 6
        ];

        if (isset($highPriorityCategories[$category])) {
            $basePriority = $highPriorityCategories[$category];
        }

        // Adjust based on count
        if ($count > 50) $basePriority += 3;
        elseif ($count > 20) $basePriority += 2;
        elseif ($count > 10) $basePriority += 1;

        return $basePriority;
    }

    private function suggestAction($category, $field)
    {
        $actions = [
            'migration_schema' => 'Create migration to rename column',
            'migration_index' => 'Update index definitions',
            'model_fillable' => 'Update fillable arrays',
            'controller_query' => 'Modify database queries',
            'blade_template' => 'Update Blade template references',
            'seeder' => 'Update seeder data',
            'javascript' => 'Update JS/Vue components',
            'model_method' => 'Review and update model methods',
        ];

        return $actions[$category] ?? 'Manual review and fix';
    }

    private function generateCategoryBreakdown()
    {
        $allCategories = [];

        foreach ($this->stats as $field => $data) {
            if (is_array($data) && isset($data['categories'])) {
                foreach ($data['categories'] as $category => $count) {
                    if (!isset($allCategories[$category])) {
                        $allCategories[$category] = [];
                    }
                    $allCategories[$category][$field] = $count;
                }
            }
        }

        foreach ($allCategories as $category => $fields) {
            $totalInCategory = array_sum($fields);
            echo "├── $category: $totalInCategory violations\n";

            foreach ($fields as $field => $count) {
                echo "│   ├── $field: $count\n";
            }
        }
    }
}

// Execute the compliance check
$checker = new Context7FinalComplianceChecker(__DIR__);
$violations = $checker->runFullComplianceCheck();

echo "\n✅ Final compliance check completed!\n";
echo "📄 Total violations found: " . count($violations) . "\n\n";
