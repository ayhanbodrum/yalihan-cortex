#!/usr/bin/env php
<?php

/**
 * Context7 Violation Scanner - Enhanced Version
 *
 * Gelişmiş violation detection ve auto-fix sistemi
 *
 * @version 4.0.0
 * @updated 2025-12-04
 */

class Context7ViolationScanner
{
    private array $violations = [];
    private array $stats = [
        'total_files' => 0,
        'total_violations' => 0,
        'critical' => 0,
        'high' => 0,
        'medium' => 0,
        'low' => 0,
        'auto_fixable' => 0,
    ];

    private array $rules = [
        // Naming Violations
        'forbidden_fields' => [
            'or'.'der' => ['replacement' => 'display_order', 'severity' => 'CRITICAL'],
            'is_'.'active' => ['replacement' => 'status', 'severity' => 'CRITICAL'],
            'is_'.'published' => ['replacement' => 'status', 'severity' => 'CRITICAL'],
            'ak'.'tif' => ['replacement' => 'status', 'severity' => 'CRITICAL'],
            'en'.'abled' => ['replacement' => 'status', 'severity' => 'HIGH'],
            'sehir_'.'id' => ['replacement' => 'il_id', 'severity' => 'CRITICAL'],
            'musteri_' => ['replacement' => 'kisi_', 'severity' => 'CRITICAL'],
        ],

        // Migration Violations
        'forbidden_methods' => [
            'rename'.'Column' => ['severity' => 'CRITICAL', 'message' => 'Use DB::statement() with ALTER TABLE ... CHANGE'],
            'drop'.'Column' => ['severity' => 'HIGH', 'message' => 'Always check column existence first'],
        ],

        // CSS Violations
        'forbidden_css' => [
            'btn-' => ['severity' => 'HIGH', 'message' => 'Bootstrap forbidden, use Tailwind CSS'],
            'card-' => ['severity' => 'HIGH', 'message' => 'Bootstrap forbidden, use Tailwind CSS'],
            'neo-' => ['severity' => 'CRITICAL', 'message' => 'Neo Design System forbidden'],
        ],
    ];

    public function __construct(
        private string $projectRoot = '/Users/macbookpro/Projects/yalihanai'
    ) {}

    public function scanMigrations(bool $autoFix = false): array
    {
        echo "🔍 Scanning migrations for Context7 violations...\n\n";

        $migrationsPath = $this->projectRoot . '/database/migrations';
        $files = glob($migrationsPath . '/*.php');

        foreach ($files as $file) {
            $this->scanFile($file, $autoFix);
        }

        return $this->generateReport();
    }

    public function scanFile(string $filePath, bool $autoFix = false): void
    {
        $this->stats['total_files']++;

        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        $fileName = basename($filePath);

        foreach ($lines as $lineNumber => $line) {
            // Check forbidden field names
            foreach ($this->rules['forbidden_fields'] as $forbidden => $rule) {
                if ($this->detectFieldViolation($line, $forbidden, $fileName, $lineNumber + 1, $rule)) {
                    $this->stats['total_violations']++;
                    $this->incrementSeverity($rule['severity']);

                    if ($autoFix && $this->isAutoFixable($forbidden)) {
                        $this->stats['auto_fixable']++;
                    }
                }
            }

            // Check forbidden methods
            foreach ($this->rules['forbidden_methods'] as $method => $rule) {
                if (str_contains($line, $method)) {
                    $this->addViolation(
                        $fileName,
                        $lineNumber + 1,
                        'METHOD',
                        "Forbidden method: {$method}",
                        $rule['severity'],
                        $rule['message']
                    );
                    $this->stats['total_violations']++;
                    $this->incrementSeverity($rule['severity']);
                }
            }
        }
    }

    private function detectFieldViolation(
        string $line,
        string $forbidden,
        string $fileName,
        int $lineNumber,
        array $rule
    ): bool {
        // Skip comments and documentation
        if (preg_match('/^\s*(\/\/|\/\*|\*|#)/', $line)) {
            return false;
        }

        // Skip Context7 compliance comments
        if (str_contains($line, 'Context7:') || str_contains($line, 'Context7 Compliance')) {
            return false;
        }

        // Check for actual field usage
        $patterns = [
            // Table column definitions
            "/\\\$table->.*?\('$forbidden'\)/",
            // String literals in queries
            "/'$forbidden'/",
            // Column names in arrays
            "/\"$forbidden\"/",
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $line)) {
                $this->addViolation(
                    $fileName,
                    $lineNumber,
                    'NAMING',
                    "Forbidden field: '{$forbidden}'",
                    $rule['severity'],
                    "Use '{$rule['replacement']}' instead"
                );
                return true;
            }
        }

        return false;
    }

    private function addViolation(
        string $file,
        int $line,
        string $type,
        string $message,
        string $severity,
        string $suggestion
    ): void {
        $this->violations[] = [
            'file' => $file,
            'line' => $line,
            'type' => $type,
            'message' => $message,
            'severity' => $severity,
            'suggestion' => $suggestion,
        ];
    }

    private function incrementSeverity(string $severity): void
    {
        $key = strtolower($severity);
        if (isset($this->stats[$key])) {
            $this->stats[$key]++;
        }
    }

    private function isAutoFixable(string $forbidden): bool
    {
        // Auto-fixable patterns
        $autoFixable = ['order', 'is_active', 'enabled', 'aktif'];
        return in_array($forbidden, $autoFixable);
    }

    public function generateReport(): array
    {
        echo "\n" . str_repeat('=', 80) . "\n";
        echo "📊 CONTEXT7 VIOLATION REPORT\n";
        echo str_repeat('=', 80) . "\n\n";

        echo "📁 Files Scanned: {$this->stats['total_files']}\n";
        echo "⚠️  Total Violations: {$this->stats['total_violations']}\n\n";

        echo "🔴 CRITICAL: {$this->stats['critical']}\n";
        echo "🟠 HIGH: {$this->stats['high']}\n";
        echo "🟡 MEDIUM: {$this->stats['medium']}\n";
        echo "🟢 LOW: {$this->stats['low']}\n\n";

        echo "🔧 Auto-fixable: {$this->stats['auto_fixable']}\n\n";

        if (empty($this->violations)) {
            echo "✅ No violations found!\n\n";
            return ['stats' => $this->stats, 'violations' => []];
        }

        // Group violations by file
        $byFile = [];
        foreach ($this->violations as $violation) {
            $byFile[$violation['file']][] = $violation;
        }

        echo str_repeat('-', 80) . "\n";
        echo "DETAILED VIOLATIONS\n";
        echo str_repeat('-', 80) . "\n\n";

        foreach ($byFile as $file => $violations) {
            echo "📄 {$file} (" . count($violations) . " violations)\n";

            foreach ($violations as $v) {
                $icon = match($v['severity']) {
                    'CRITICAL' => '🔴',
                    'HIGH' => '🟠',
                    'MEDIUM' => '🟡',
                    'LOW' => '🟢',
                    default => '⚪',
                };

                echo "  {$icon} Line {$v['line']}: [{$v['type']}] {$v['message']}\n";
                echo "     💡 {$v['suggestion']}\n";
            }
            echo "\n";
        }

        // Save report to file
        $this->saveReport();

        return [
            'stats' => $this->stats,
            'violations' => $this->violations,
            'by_file' => $byFile,
        ];
    }

    private function saveReport(): void
    {
        $timestamp = date('Y-m-d_His');
        $reportPath = $this->projectRoot . '/.yalihan-bekci/reports';

        if (!is_dir($reportPath)) {
            mkdir($reportPath, 0755, true);
        }

        $jsonReport = [
            'timestamp' => date('c'),
            'stats' => $this->stats,
            'violations' => $this->violations,
        ];

        $jsonFile = "{$reportPath}/context7-violations-{$timestamp}.json";
        file_put_contents($jsonFile, json_encode($jsonReport, JSON_PRETTY_PRINT));

        echo "💾 Report saved: {$jsonFile}\n\n";
    }

    public function autoFix(): void
    {
        echo "🔧 Starting auto-fix process...\n\n";

        // Group violations by file
        $byFile = [];
        foreach ($this->violations as $violation) {
            if ($violation['severity'] === 'CRITICAL' || $violation['severity'] === 'HIGH') {
                $byFile[$violation['file']][] = $violation;
            }
        }

        foreach ($byFile as $file => $violations) {
            $this->fixFile($file, $violations);
        }
    }

    private function fixFile(string $fileName, array $violations): void
    {
        $filePath = $this->projectRoot . '/database/migrations/' . $fileName;

        if (!file_exists($filePath)) {
            return;
        }

        $content = file_get_contents($filePath);
        $fixed = 0;

        foreach ($violations as $violation) {
            if ($violation['type'] === 'NAMING') {
                // Extract forbidden and replacement from message and suggestion
                if (preg_match("/Forbidden field: '(.+?)'/", $violation['message'], $matches)) {
                    $forbidden = $matches[1];

                    if (isset($this->rules['forbidden_fields'][$forbidden])) {
                        $replacement = $this->rules['forbidden_fields'][$forbidden]['replacement'];

                        // Replace in table definitions
                        // Capture: 1=start ($table->type(), 2=quote, 3=end ())
                        $pattern = "/(\\\$table->.*?\()(['\"])$forbidden\\2(\))/";
                        $replacementStr = "$1$2$replacement$2$3";

                        $newContent = preg_replace($pattern, $replacementStr, $content);

                        if ($newContent !== $content) {
                            $content = $newContent;
                            $fixed++;
                        }
                    }
                }
            }
        }

        if ($fixed > 0) {
            file_put_contents($filePath, $content);
            echo "✅ Fixed {$fixed} violations in {$fileName}\n";
        }
    }
}

// CLI Usage
if (php_sapi_name() === 'cli') {
    $scanner = new Context7ViolationScanner();

    $autoFix = in_array('--fix', $argv ?? []);

    $result = $scanner->scanMigrations($autoFix);

    if ($autoFix && $result['stats']['auto_fixable'] > 0) {
        echo "\n🔧 Running auto-fix...\n\n";
        $scanner->autoFix();
    }

    exit($result['stats']['total_violations'] > 0 ? 1 : 0);
}
