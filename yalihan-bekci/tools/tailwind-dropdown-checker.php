#!/usr/bin/env php
<?php
/**
 * Tailwind Dropdown Readability Checker
 * Yalıhan Bekçi - Automated Quality Assurance Tool
 *
 * Purpose: Scan all Blade files for dropdown readability issues
 * Date: 2025-11-01
 * Version: 1.0.0
 */

class TailwindDropdownChecker
{
    private $basePath;
    private $issues = [];
    private $scannedFiles = 0;
    private $totalDropdowns = 0;
    private $passedDropdowns = 0;
    private $failedDropdowns = 0;

    public function __construct($basePath = null)
    {
        $this->basePath = $basePath ?? dirname(__DIR__, 2);
    }

    public function scan($path = 'resources/views/admin')
    {
        echo "🔍 Tailwind Dropdown Readability Checker\n";
        echo "==========================================\n\n";

        $fullPath = $this->basePath . '/' . $path;
        $files = $this->getBladeFiles($fullPath);

        echo "📁 Scanning: $path\n";
        echo "📄 Found " . count($files) . " Blade files\n\n";

        foreach ($files as $file) {
            $this->scanFile($file);
        }

        $this->generateReport();
    }

    private function getBladeFiles($directory)
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function scanFile($filePath)
    {
        $this->scannedFiles++;
        $content = file_get_contents($filePath);
        $relativePath = str_replace($this->basePath . '/', '', $filePath);

        // Find all select tags
        preg_match_all('/<select[^>]*>/i', $content, $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[0] as $match) {
            $this->totalDropdowns++;
            $selectTag = $match[0];
            $lineNumber = $this->getLineNumber($content, $match[1]);

            $violations = $this->checkDropdown($selectTag);

            if (!empty($violations)) {
                $this->failedDropdowns++;
                $this->issues[] = [
                    'file' => $relativePath,
                    'line' => $lineNumber,
                    'tag' => $selectTag,
                    'violations' => $violations
                ];
            } else {
                $this->passedDropdowns++;
            }
        }
    }

    private function checkDropdown($selectTag)
    {
        $violations = [];

        // Check 1: dark:bg-gray-800 (should be dark:bg-gray-900)
        if (preg_match('/dark:bg-gray-800/', $selectTag)) {
            $violations[] = [
                'check_id' => 'TC002',
                'severity' => 'CRITICAL',
                'issue' => 'dark:bg-gray-800 kullanılmış',
                'fix' => 'dark:bg-gray-900 olmalı',
                'reason' => 'Dropdown option\'ları dark mode\'da okunmuyor'
            ];
        }

        // Check 2: dark:text-gray-100 (should be dark:text-white)
        if (preg_match('/dark:text-gray-100/', $selectTag)) {
            $violations[] = [
                'check_id' => 'TC003',
                'severity' => 'CRITICAL',
                'issue' => 'dark:text-gray-100 kullanılmış',
                'fix' => 'dark:text-white olmalı',
                'reason' => 'Text kontrast yetersiz, okunmuyor'
            ];
        }

        // Check 3: Missing color-scheme property
        if (!preg_match('/style\s*=\s*["\'].*color-scheme:\s*light\s+dark/i', $selectTag)) {
            $violations[] = [
                'check_id' => 'TC004',
                'severity' => 'HIGH',
                'issue' => 'color-scheme property eksik',
                'fix' => 'style="color-scheme: light dark;" ekle',
                'reason' => 'Browser native dropdown dark mode render edilmiyor'
            ];
        }

        // Check 4: Neo Design class (should NOT exist)
        if (preg_match('/neo-(btn|select|input|form|card)/', $selectTag)) {
            $violations[] = [
                'check_id' => 'TC001',
                'severity' => 'CRITICAL',
                'issue' => 'Neo Design class tespit edildi',
                'fix' => 'Pure Tailwind CSS kullan',
                'reason' => 'Neo Design System kaldırıldı (2025-11-01)'
            ];
        }

        // Check 5: Missing transition
        if (!preg_match('/transition-/', $selectTag)) {
            $violations[] = [
                'check_id' => 'TC006',
                'severity' => 'MEDIUM',
                'issue' => 'transition class eksik',
                'fix' => 'transition-all duration-200 ekle',
                'reason' => 'Etkileşimli elementlerde transition zorunlu'
            ];
        }

        return $violations;
    }

    private function getLineNumber($content, $offset)
    {
        return substr_count(substr($content, 0, $offset), "\n") + 1;
    }

    private function generateReport()
    {
        echo "\n==========================================\n";
        echo "📊 SCAN RESULTS\n";
        echo "==========================================\n\n";

        echo "📄 Scanned Files: {$this->scannedFiles}\n";
        echo "🔽 Total Dropdowns Found: {$this->totalDropdowns}\n";
        echo "✅ Passed: {$this->passedDropdowns}\n";
        echo "❌ Failed: {$this->failedDropdowns}\n\n";

        if (empty($this->issues)) {
            echo "🎉 SUCCESS! All dropdowns are Context7 compliant!\n";
            return;
        }

        echo "🔴 ISSUES FOUND\n";
        echo "==========================================\n\n";

        $criticalCount = 0;
        $highCount = 0;
        $mediumCount = 0;

        foreach ($this->issues as $issue) {
            echo "📄 File: {$issue['file']}\n";
            echo "📍 Line: {$issue['line']}\n";
            echo "🔖 Tag: " . substr($issue['tag'], 0, 100) . "...\n\n";

            foreach ($issue['violations'] as $violation) {
                $severity = $violation['severity'];
                $icon = $severity === 'CRITICAL' ? '🔴' : ($severity === 'HIGH' ? '🟡' : '🟢');

                echo "   $icon [{$violation['check_id']}] {$violation['severity']}\n";
                echo "   Issue: {$violation['issue']}\n";
                echo "   Fix: {$violation['fix']}\n";
                echo "   Reason: {$violation['reason']}\n\n";

                if ($severity === 'CRITICAL') $criticalCount++;
                elseif ($severity === 'HIGH') $highCount++;
                else $mediumCount++;
            }

            echo "---\n\n";
        }

        echo "==========================================\n";
        echo "📊 VIOLATION SUMMARY\n";
        echo "==========================================\n\n";
        echo "🔴 CRITICAL: $criticalCount\n";
        echo "🟡 HIGH: $highCount\n";
        echo "🟢 MEDIUM: $mediumCount\n\n";

        $passRate = round(($this->passedDropdowns / $this->totalDropdowns) * 100, 2);
        echo "📈 PASS RATE: $passRate%\n\n";

        if ($passRate < 100) {
            echo "⚠️ ACTION REQUIRED: Fix dropdown readability issues!\n";
            echo "📖 Reference: .context7/FORM_DESIGN_STANDARDS.md v1.1.0\n\n";
        }

        // Save report
        $this->saveReport();
    }

    private function saveReport()
    {
        $reportPath = $this->basePath . '/yalihan-bekci/reports/tailwind-dropdown-scan-' . date('Y-m-d-His') . '.json';

        $report = [
            'scan_date' => date('Y-m-d H:i:s'),
            'scanned_files' => $this->scannedFiles,
            'total_dropdowns' => $this->totalDropdowns,
            'passed' => $this->passedDropdowns,
            'failed' => $this->failedDropdowns,
            'pass_rate' => round(($this->passedDropdowns / max($this->totalDropdowns, 1)) * 100, 2),
            'issues' => $this->issues
        ];

        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "💾 Report saved: " . str_replace($this->basePath . '/', '', $reportPath) . "\n";
    }
}

// Run the checker
$checker = new TailwindDropdownChecker();
$checker->scan();
