<?php

/**
 * Form Standards Checker - Context7 & Neo Design System
 *
 * Bu script tüm blade dosyalarını tarar ve form standartlarına uyumluluğunu kontrol eder
 *
 * Usage: php tools/context7/form-standards-checker.php
 */
class FormStandardsChecker
{
    private $violations = [];

    private $warnings = [];

    private $passed = [];

    private $stats = [
        'total_files' => 0,
        'total_inputs' => 0,
        'total_selects' => 0,
        'total_labels' => 0,
        'compliant_inputs' => 0,
        'compliant_selects' => 0,
        'compliant_labels' => 0,
    ];

    // Neo Design System Standards + Context7 Compliance
    private $standards = [
        'input' => [
            'required_classes' => ['w-full', 'px-4', 'py-3', 'border', 'rounded-lg', 'focus:ring-2'],
            'dark_mode_classes' => ['dark:bg-gray-700', 'dark:border-gray-600', 'dark:text-gray-100'],
            'error_class' => '@error',
        ],
        'select' => [
            'required_classes' => ['w-full', 'px-4', 'py-3', 'border', 'rounded-lg'],
            'dark_mode_classes' => ['dark:bg-gray-700', 'dark:text-gray-100'],
            'empty_option' => true,
        ],
        'label' => [
            'required_classes' => ['block', 'text-sm', 'font-medium', 'mb-2'],
            'dark_mode_classes' => ['dark:text-gray-300'],
        ],
        'toggle' => [
            'required_classes' => ['peer', 'sr-only', 'peer-checked:'],
            'container_classes' => ['flex', 'items-center', 'justify-between'],
        ],
    ];

    // Context7 Forbidden Patterns
    private $context7Forbidden = [
        'database_fields' => [
            'durum' => 'status',
            'aktif' => 'active',
            'is_active' => 'enabled',
            'sehir' => 'city',
            'musteriler' => 'kisiler',
        ],
        'css_classes' => [
            'btn-' => 'neo-btn',
            'card-' => 'neo-card',
            'form-control' => 'w-full px-4 py-3 border rounded-lg',
            'form-select' => 'w-full px-4 py-3 border rounded-lg',
        ],
        'layouts' => [
            "@extends('layouts.app')" => "@extends('admin.layouts.neo')",
        ],
    ];

    public function __construct()
    {
        echo "\n";
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "🔍 Form Standards Checker - Context7 & Neo Design System\n";
        echo "Version: 1.0.0 | Context7 v3.5.0 | Yalıhan Bekçi Enabled\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";
    }

    public function checkDirectory($directory)
    {
        $files = $this->getBladeFiles($directory);

        echo "📂 Scanning directory: {$directory}\n";
        echo '📄 Found '.count($files)." blade files\n\n";

        foreach ($files as $file) {
            $this->checkFile($file);
        }

        $this->printReport();
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

    private function checkFile($filePath)
    {
        $content = file_get_contents($filePath);
        $fileName = basename($filePath);

        $this->stats['total_files']++;

        // Check for form elements
        $hasInputs = $this->checkInputs($content, $fileName);
        $hasSelects = $this->checkSelects($content, $fileName);
        $hasLabels = $this->checkLabels($content, $fileName);

        // Check for Context7 Live Search
        $hasLiveSearch = $this->checkLiveSearch($content, $fileName);

        // Check for Alpine.js usage
        $hasAlpine = $this->checkAlpineUsage($content, $fileName);

        // ✅ Context7 Compliance Check
        $this->checkContext7Compliance($content, $fileName);

        // If file has form elements but violations, mark it
        if (($hasInputs || $hasSelects || $hasLabels) &&
            (count($this->violations) > 0 || count($this->warnings) > 0)
        ) {
            // File has issues
        } elseif ($hasInputs || $hasSelects || $hasLabels) {
            $this->passed[] = $fileName;
        }
    }

    private function checkInputs($content, $fileName)
    {
        // Find all input tags
        preg_match_all('/<input[^>]*type=["\'](?!hidden|checkbox|radio)[^"\']*["\'][^>]*>/i', $content, $matches);

        if (empty($matches[0])) {
            return false;
        }

        $this->stats['total_inputs'] += count($matches[0]);

        foreach ($matches[0] as $input) {
            $compliant = true;

            // Check required classes
            foreach ($this->standards['input']['required_classes'] as $class) {
                if (strpos($input, $class) === false) {
                    $this->violations[] = [
                        'file' => $fileName,
                        'type' => 'input',
                        'severity' => 'error',
                        'message' => "Missing required class: {$class}",
                        'element' => substr($input, 0, 100).'...',
                    ];
                    $compliant = false;
                }
            }

            // Check dark mode support
            $hasDarkMode = false;
            foreach ($this->standards['input']['dark_mode_classes'] as $class) {
                if (strpos($input, $class) !== false) {
                    $hasDarkMode = true;
                    break;
                }
            }

            if (! $hasDarkMode) {
                $this->warnings[] = [
                    'file' => $fileName,
                    'type' => 'input',
                    'severity' => 'warning',
                    'message' => 'Missing dark mode support',
                    'element' => substr($input, 0, 100).'...',
                ];
                $compliant = false;
            }

            if ($compliant) {
                $this->stats['compliant_inputs']++;
            }
        }

        return true;
    }

    private function checkSelects($content, $fileName)
    {
        // Find all select tags
        preg_match_all('/<select[^>]*>.*?<\/select>/is', $content, $matches);

        if (empty($matches[0])) {
            return false;
        }

        $this->stats['total_selects'] += count($matches[0]);

        foreach ($matches[0] as $select) {
            $compliant = true;

            // Check required classes
            foreach ($this->standards['select']['required_classes'] as $class) {
                if (strpos($select, $class) === false) {
                    $this->violations[] = [
                        'file' => $fileName,
                        'type' => 'select',
                        'severity' => 'error',
                        'message' => "Missing required class: {$class}",
                        'element' => substr($select, 0, 100).'...',
                    ];
                    $compliant = false;
                }
            }

            // Check for empty option
            if (! preg_match('/<option[^>]*value=["\']["\']/', $select)) {
                $this->warnings[] = [
                    'file' => $fileName,
                    'type' => 'select',
                    'severity' => 'warning',
                    'message' => 'Missing empty first option (best practice)',
                    'element' => substr($select, 0, 100).'...',
                ];
            }

            // Check dark mode support
            $hasDarkMode = false;
            foreach ($this->standards['select']['dark_mode_classes'] as $class) {
                if (strpos($select, $class) !== false) {
                    $hasDarkMode = true;
                    break;
                }
            }

            if (! $hasDarkMode) {
                $this->warnings[] = [
                    'file' => $fileName,
                    'type' => 'select',
                    'severity' => 'warning',
                    'message' => 'Missing dark mode support',
                ];
                $compliant = false;
            }

            if ($compliant) {
                $this->stats['compliant_selects']++;
            }
        }

        return true;
    }

    private function checkLabels($content, $fileName)
    {
        // Find all label tags
        preg_match_all('/<label[^>]*>.*?<\/label>/is', $content, $matches);

        if (empty($matches[0])) {
            return false;
        }

        $this->stats['total_labels'] += count($matches[0]);

        foreach ($matches[0] as $label) {
            $compliant = true;

            // Check required classes
            $missingClasses = [];
            foreach ($this->standards['label']['required_classes'] as $class) {
                if (strpos($label, $class) === false) {
                    $missingClasses[] = $class;
                    $compliant = false;
                }
            }

            if (! empty($missingClasses)) {
                $this->violations[] = [
                    'file' => $fileName,
                    'type' => 'label',
                    'severity' => 'error',
                    'message' => 'Missing required classes: '.implode(', ', $missingClasses),
                    'element' => substr($label, 0, 100).'...',
                ];
            }

            if ($compliant) {
                $this->stats['compliant_labels']++;
            }
        }

        return true;
    }

    private function checkLiveSearch($content, $fileName)
    {
        // Check for Context7 Live Search usage
        if (strpos($content, 'context7-live-search') !== false) {
            // Check if it's properly initialized
            if (strpos($content, 'data-search-type') === false) {
                $this->violations[] = [
                    'file' => $fileName,
                    'type' => 'live-search',
                    'severity' => 'error',
                    'message' => 'Context7 Live Search found but missing data-search-type attribute',
                ];
            }

            return true;
        }

        return false;
    }

    private function checkAlpineUsage($content, $fileName)
    {
        // Check for problematic Alpine.js patterns
        if (preg_match('/@submit\.prevent=["\']submitForm/', $content)) {
            $this->warnings[] = [
                'file' => $fileName,
                'type' => 'alpine',
                'severity' => 'warning',
                'message' => 'Using @submit.prevent with custom handler - consider normal form submission',
            ];
        }

        return strpos($content, 'x-data') !== false || strpos($content, '@') !== false;
    }

    private function checkContext7Compliance($content, $fileName)
    {
        // Check for forbidden database fields
        foreach ($this->context7Forbidden['database_fields'] as $forbidden => $replacement) {
            if (preg_match("/name=[\"']{$forbidden}[\"']/", $content)) {
                $this->violations[] = [
                    'file' => $fileName,
                    'type' => 'context7-field',
                    'severity' => 'error',
                    'message' => "Context7 Violation: Field '{$forbidden}' should be '{$replacement}'",
                ];
            }
        }

        // Check for forbidden CSS classes
        foreach ($this->context7Forbidden['css_classes'] as $forbidden => $replacement) {
            if (strpos($content, $forbidden) !== false && $forbidden !== 'form-control' && $forbidden !== 'form-select') {
                // Only check class attributes
                if (preg_match("/class=[\"'][^\"']*{$forbidden}/", $content)) {
                    $this->warnings[] = [
                        'file' => $fileName,
                        'type' => 'context7-css',
                        'severity' => 'warning',
                        'message' => "Context7 Warning: CSS class '{$forbidden}' should use '{$replacement}'",
                    ];
                }
            }
        }

        // Check for forbidden layouts
        foreach ($this->context7Forbidden['layouts'] as $forbidden => $replacement) {
            if (strpos($content, $forbidden) !== false) {
                $this->violations[] = [
                    'file' => $fileName,
                    'type' => 'context7-layout',
                    'severity' => 'error',
                    'message' => "Context7 Violation: {$forbidden} should use {$replacement}",
                ];
            }
        }
    }

    private function printReport()
    {
        echo "\n═══════════════════════════════════════════════════════════════\n";
        echo "📊 STATISTICS\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        echo "Total Files Scanned:     {$this->stats['total_files']}\n";
        echo "Total Inputs Found:      {$this->stats['total_inputs']}\n";
        echo "Total Selects Found:     {$this->stats['total_selects']}\n";
        echo "Total Labels Found:      {$this->stats['total_labels']}\n\n";

        $inputCompliance = $this->stats['total_inputs'] > 0
            ? round(($this->stats['compliant_inputs'] / $this->stats['total_inputs']) * 100, 2)
            : 0;
        $selectCompliance = $this->stats['total_selects'] > 0
            ? round(($this->stats['compliant_selects'] / $this->stats['total_selects']) * 100, 2)
            : 0;
        $labelCompliance = $this->stats['total_labels'] > 0
            ? round(($this->stats['compliant_labels'] / $this->stats['total_labels']) * 100, 2)
            : 0;

        echo "Input Compliance:        {$inputCompliance}% ({$this->stats['compliant_inputs']}/{$this->stats['total_inputs']})\n";
        echo "Select Compliance:       {$selectCompliance}% ({$this->stats['compliant_selects']}/{$this->stats['total_selects']})\n";
        echo "Label Compliance:        {$labelCompliance}% ({$this->stats['compliant_labels']}/{$this->stats['total_labels']})\n\n";

        $overallCompliance = ($inputCompliance + $selectCompliance + $labelCompliance) / 3;
        $statusIcon = $overallCompliance >= 90 ? '✅' : ($overallCompliance >= 70 ? '⚠️' : '❌');

        echo "Overall Compliance:      {$statusIcon} ".round($overallCompliance, 2)."%\n\n";

        // Print violations
        if (! empty($this->violations)) {
            echo "═══════════════════════════════════════════════════════════════\n";
            echo '🚨 VIOLATIONS ('.count($this->violations).")\n";
            echo "═══════════════════════════════════════════════════════════════\n\n";

            $violationsByFile = [];
            foreach ($this->violations as $violation) {
                $violationsByFile[$violation['file']][] = $violation;
            }

            foreach ($violationsByFile as $file => $fileViolations) {
                echo "📄 {$file} (".count($fileViolations)." violations)\n";
                foreach ($fileViolations as $violation) {
                    echo "   ❌ [{$violation['type']}] {$violation['message']}\n";
                }
                echo "\n";
            }
        }

        // Print warnings
        if (! empty($this->warnings)) {
            echo "═══════════════════════════════════════════════════════════════\n";
            echo '⚠️  WARNINGS ('.count($this->warnings).")\n";
            echo "═══════════════════════════════════════════════════════════════\n\n";

            $warningsByFile = [];
            foreach ($this->warnings as $warning) {
                $warningsByFile[$warning['file']][] = $warning;
            }

            foreach ($warningsByFile as $file => $fileWarnings) {
                echo "📄 {$file} (".count($fileWarnings)." warnings)\n";
                foreach ($fileWarnings as $warning) {
                    echo "   ⚠️  [{$warning['type']}] {$warning['message']}\n";
                }
                echo "\n";
            }
        }

        // Print passed files
        if (! empty($this->passed)) {
            echo "═══════════════════════════════════════════════════════════════\n";
            echo '✅ COMPLIANT FILES ('.count($this->passed).")\n";
            echo "═══════════════════════════════════════════════════════════════\n\n";

            foreach ($this->passed as $file) {
                echo "   ✅ {$file}\n";
            }
            echo "\n";
        }

        echo "═══════════════════════════════════════════════════════════════\n";
        echo "📖 RECOMMENDATIONS\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        if ($overallCompliance < 90) {
            echo "📝 Please review: docs/context7/FORM-DESIGN-STANDARDS-2025-10-14.md\n";
            echo "🔧 Use standard snippets from the documentation\n";
            echo "🌙 Ensure all form elements have dark mode support\n";
            echo "📱 Test responsive layouts (grid-cols-1 md:grid-cols-2)\n\n";
        } else {
            echo "🎉 Great job! Your forms are highly compliant with Neo Design System!\n\n";
        }
    }
}

// Run the checker
$checker = new FormStandardsChecker;

// Check specific directories
$directories = [
    'resources/views/admin/ilanlar',
    'resources/views/admin/users',
    'resources/views/admin/danisman',
    'resources/views/admin/kisiler',
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $checker->checkDirectory($dir);
    }
}
