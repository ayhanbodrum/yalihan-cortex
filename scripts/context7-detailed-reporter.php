#!/usr/bin/env php
<?php

/**
 * Context7 Detailed Violation Reporter
 *
 * Kopyalanabilir, detaylı violation raporu oluşturur
 * Hata bildirimi için kaynak kod snippet'leri içerir
 *
 * @version 1.0.0
 * @updated 2025-12-04
 */

class Context7DetailedReporter
{
    private array $violations = [];
    private array $stats = [
        'total_files' => 0,
        'total_violations' => 0,
        'critical' => 0,
        'high' => 0,
        'medium' => 0,
        'low' => 0,
    ];

    private array $rules = [
        'forbidden_fields' => [
            'or'.'der' => ['replacement' => 'display_order', 'severity' => 'CRITICAL'],
            'is_'.'active' => ['replacement' => 'status', 'severity' => 'CRITICAL'],
            'is_'.'published' => ['replacement' => 'status', 'severity' => 'CRITICAL'],
            'ak'.'tif' => ['replacement' => 'status', 'severity' => 'CRITICAL'],
            'en'.'abled' => ['replacement' => 'status', 'severity' => 'HIGH'],
            'sehir_'.'id' => ['replacement' => 'il_id', 'severity' => 'CRITICAL'],
        ],
        'forbidden_methods' => [
            'rename'.'Column' => ['severity' => 'CRITICAL', 'message' => 'Use DB::statement() with ALTER TABLE ... CHANGE'],
            'drop'.'Column' => ['severity' => 'HIGH', 'message' => 'Always check column existence with Schema::hasColumn() first'],
        ],
    ];

    public function __construct(
        private string $projectRoot = '/Users/macbookpro/Projects/yalihanai'
    ) {}

    public function scanAndReport(bool $includeSnippets = true): void
    {
        echo "🔍 Scanning migrations for Context7 violations...\n\n";

        $migrationsPath = $this->projectRoot . '/database/migrations';
        $files = glob($migrationsPath . '/*.php');

        foreach ($files as $file) {
            $this->scanFile($file, $includeSnippets);
        }

        $this->generateDetailedReport();
        $this->generateCopyableReport();
        $this->generateMarkdownReport();
    }

    private function scanFile(string $filePath, bool $includeSnippets): void
    {
        $this->stats['total_files']++;

        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        $fileName = basename($filePath);

        foreach ($lines as $lineNumber => $line) {
            // Skip comments
            if (preg_match('/^\s*(\/\/|\/\*|\*|#)/', $line)) {
                continue;
            }

            // Skip Context7 compliance comments
            if (str_contains($line, 'Context7:') || str_contains($line, 'Context7 Compliance')) {
                continue;
            }

            // Check forbidden field names
            foreach ($this->rules['forbidden_fields'] as $forbidden => $rule) {
                if ($this->detectFieldViolation($line, $forbidden)) {
                    $snippet = $includeSnippets ? $this->getCodeSnippet($lines, $lineNumber) : null;

                    $this->addViolation(
                        $fileName,
                        $filePath,
                        $lineNumber + 1,
                        'NAMING',
                        "Forbidden field: '{$forbidden}'",
                        $rule['severity'],
                        "Use '{$rule['replacement']}' instead",
                        $line,
                        $snippet,
                        $this->generateFixExample($forbidden, $rule['replacement'], $line)
                    );
                }
            }

            // Check forbidden methods
            foreach ($this->rules['forbidden_methods'] as $method => $rule) {
                if (str_contains($line, $method)) {
                    $snippet = $includeSnippets ? $this->getCodeSnippet($lines, $lineNumber) : null;

                    $this->addViolation(
                        $fileName,
                        $filePath,
                        $lineNumber + 1,
                        'METHOD',
                        "Forbidden method: {$method}()",
                        $rule['severity'],
                        $rule['message'],
                        $line,
                        $snippet,
                        $this->generateMethodFixExample($method, $line)
                    );
                }
            }
        }
    }

    private function detectFieldViolation(string $line, string $forbidden): bool
    {
        $patterns = [
            "/\\\$table->.*?\('$forbidden'\)/",
            "/'$forbidden'/",
            "/\"$forbidden\"/",
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }

        return false;
    }

    private function getCodeSnippet(array $lines, int $lineNumber, int $context = 3): array
    {
        $start = max(0, $lineNumber - $context);
        $end = min(count($lines) - 1, $lineNumber + $context);

        $snippet = [];
        for ($i = $start; $i <= $end; $i++) {
            $snippet[] = [
                'line' => $i + 1,
                'code' => $lines[$i],
                'is_violation' => $i === $lineNumber,
            ];
        }

        return $snippet;
    }

    private function generateFixExample(string $forbidden, string $replacement, string $originalLine): string
    {
        return str_replace($forbidden, $replacement, $originalLine);
    }

    private function generateMethodFixExample(string $method, string $originalLine): ?string
    {
        if ($method === 'renameColumn') {
            return <<<'PHP'
// ❌ WRONG
$table->renameColumn('old_name', 'new_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'old_name')) {
    DB::statement("ALTER TABLE table_name CHANGE `old_name` `new_name` VARCHAR(255) NOT NULL");
}
PHP;
        }

        if ($method === 'dropColumn') {
            return <<<'PHP'
// ❌ WRONG
$table->dropColumn('column_name');

// ✅ CORRECT
if (Schema::hasColumn('table_name', 'column_name')) {
    $table->dropColumn('column_name');
}
PHP;
        }

        return null;
    }

    private function addViolation(
        string $file,
        string $fullPath,
        int $line,
        string $type,
        string $message,
        string $severity,
        string $suggestion,
        string $originalCode,
        ?array $snippet,
        ?string $fixExample
    ): void {
        $this->violations[] = [
            'file' => $file,
            'full_path' => $fullPath,
            'line' => $line,
            'type' => $type,
            'message' => $message,
            'severity' => $severity,
            'suggestion' => $suggestion,
            'original_code' => trim($originalCode),
            'snippet' => $snippet,
            'fix_example' => $fixExample,
        ];

        $this->stats['total_violations']++;
        $this->incrementSeverity($severity);
    }

    private function incrementSeverity(string $severity): void
    {
        $key = strtolower($severity);
        if (isset($this->stats[$key])) {
            $this->stats[$key]++;
        }
    }

    private function generateDetailedReport(): void
    {
        echo "\n" . str_repeat('=', 100) . "\n";
        echo "📊 CONTEXT7 DETAILED VIOLATION REPORT\n";
        echo str_repeat('=', 100) . "\n\n";

        echo "📅 Generated: " . date('Y-m-d H:i:s') . "\n";
        echo "📁 Files Scanned: {$this->stats['total_files']}\n";
        echo "⚠️  Total Violations: {$this->stats['total_violations']}\n\n";

        echo "🔴 CRITICAL: {$this->stats['critical']}\n";
        echo "🟠 HIGH: {$this->stats['high']}\n";
        echo "🟡 MEDIUM: {$this->stats['medium']}\n";
        echo "🟢 LOW: {$this->stats['low']}\n\n";

        if (empty($this->violations)) {
            echo "✅ No violations found!\n\n";
            return;
        }

        echo str_repeat('-', 100) . "\n";
        echo "DETAILED VIOLATIONS WITH CODE SNIPPETS\n";
        echo str_repeat('-', 100) . "\n\n";

        foreach ($this->violations as $index => $v) {
            $icon = match($v['severity']) {
                'CRITICAL' => '🔴',
                'HIGH' => '🟠',
                'MEDIUM' => '🟡',
                'LOW' => '🟢',
                default => '⚪',
            };

            echo "#{" . ($index + 1) . "} {$icon} {$v['severity']} - {$v['type']}\n";
            echo str_repeat('-', 100) . "\n";
            echo "📄 File: {$v['file']}\n";
            echo "📍 Line: {$v['line']}\n";
            echo "⚠️  Issue: {$v['message']}\n";
            echo "💡 Suggestion: {$v['suggestion']}\n\n";

            echo "📝 Original Code:\n";
            echo "```php\n";
            echo $v['original_code'] . "\n";
            echo "```\n\n";

            if ($v['snippet']) {
                echo "📋 Code Context:\n";
                echo "```php\n";
                foreach ($v['snippet'] as $s) {
                    $marker = $s['is_violation'] ? '>>> ' : '    ';
                    printf("%s%4d | %s\n", $marker, $s['line'], $s['code']);
                }
                echo "```\n\n";
            }

            if ($v['fix_example']) {
                echo "✅ Fix Example:\n";
                echo "```php\n";
                echo $v['fix_example'] . "\n";
                echo "```\n\n";
            }

            echo "🔗 Full Path: {$v['full_path']}\n";
            echo "\n" . str_repeat('=', 100) . "\n\n";
        }
    }

    private function generateCopyableReport(): void
    {
        $timestamp = date('Y-m-d_His');
        $reportPath = $this->projectRoot . '/.yalihan-bekci/reports';

        if (!is_dir($reportPath)) {
            mkdir($reportPath, 0755, true);
        }

        // JSON Report (Detailed)
        $jsonReport = [
            'generated_at' => date('c'),
            'stats' => $this->stats,
            'violations' => $this->violations,
        ];

        $jsonFile = "{$reportPath}/context7-detailed-{$timestamp}.json";
        file_put_contents($jsonFile, json_encode($jsonReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "💾 JSON Report saved: {$jsonFile}\n";

        // CSV Report (For Excel/Spreadsheet)
        $csvFile = "{$reportPath}/context7-violations-{$timestamp}.csv";
        $fp = fopen($csvFile, 'w');

        // CSV Header
        fputcsv($fp, [
            'ID',
            'Severity',
            'Type',
            'File',
            'Line',
            'Message',
            'Suggestion',
            'Original Code',
            'Full Path'
        ], ',', '"', '\\');

        // CSV Data
        foreach ($this->violations as $index => $v) {
            fputcsv($fp, [
                $index + 1,
                $v['severity'],
                $v['type'],
                $v['file'],
                $v['line'],
                $v['message'],
                $v['suggestion'],
                $v['original_code'],
                $v['full_path']
            ], ',', '"', '\\');
        }

        fclose($fp);
        echo "💾 CSV Report saved: {$csvFile}\n";
    }

    private function generateMarkdownReport(): void
    {
        $timestamp = date('Y-m-d_His');
        $reportPath = $this->projectRoot . '/.yalihan-bekci/reports';

        $mdFile = "{$reportPath}/context7-violations-{$timestamp}.md";

        $md = "# Context7 Violation Report\n\n";
        $md .= "**Generated:** " . date('Y-m-d H:i:s') . "\n\n";
        $md .= "## Summary\n\n";
        $md .= "| Metric | Count |\n";
        $md .= "|--------|-------|\n";
        $md .= "| Files Scanned | {$this->stats['total_files']} |\n";
        $md .= "| Total Violations | {$this->stats['total_violations']} |\n";
        $md .= "| 🔴 Critical | {$this->stats['critical']} |\n";
        $md .= "| 🟠 High | {$this->stats['high']} |\n";
        $md .= "| 🟡 Medium | {$this->stats['medium']} |\n";
        $md .= "| 🟢 Low | {$this->stats['low']} |\n\n";

        $md .= "## Violations by Severity\n\n";

        // Group by severity
        $bySeverity = [];
        foreach ($this->violations as $v) {
            $bySeverity[$v['severity']][] = $v;
        }

        foreach (['CRITICAL', 'HIGH', 'MEDIUM', 'LOW'] as $severity) {
            if (empty($bySeverity[$severity])) continue;

            $icon = match($severity) {
                'CRITICAL' => '🔴',
                'HIGH' => '🟠',
                'MEDIUM' => '🟡',
                'LOW' => '🟢',
                default => '⚪',
            };

            $md .= "### {$icon} {$severity} (" . count($bySeverity[$severity]) . ")\n\n";

            foreach ($bySeverity[$severity] as $index => $v) {
                $md .= "#### " . ($index + 1) . ". {$v['file']}:{$v['line']}\n\n";
                $md .= "- **Type:** {$v['type']}\n";
                $md .= "- **Issue:** {$v['message']}\n";
                $md .= "- **Suggestion:** {$v['suggestion']}\n\n";
                $md .= "**Original Code:**\n";
                $md .= "```php\n{$v['original_code']}\n```\n\n";

                if ($v['fix_example']) {
                    $md .= "**Fix Example:**\n";
                    $md .= "```php\n{$v['fix_example']}\n```\n\n";
                }

                $md .= "---\n\n";
            }
        }

        file_put_contents($mdFile, $md);
        echo "💾 Markdown Report saved: {$mdFile}\n\n";
    }
}

// CLI Usage
if (php_sapi_name() === 'cli') {
    $reporter = new Context7DetailedReporter();
    $reporter->scanAndReport(includeSnippets: true);
}
