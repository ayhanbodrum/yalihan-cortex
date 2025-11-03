<?php
/**
 * Comprehensive Form Standards Migration
 * 
 * Tüm admin Blade dosyalarındaki form elemanlarını FormStandards'a göre migrate eder.
 * Alt sayfalar dahil tüm dizinleri tarar.
 * 
 * Usage:
 * php scripts/comprehensive-form-migration.php --dry-run
 * php scripts/comprehensive-form-migration.php
 * 
 * @version 2.0.0
 * @since 2025-11-02
 */

$isDryRun = in_array('--dry-run', $argv);

echo "\n";
echo "========================================================\n";
echo "🚀 COMPREHENSIVE FORM STANDARDS MIGRATION\n";
echo "========================================================\n";
echo "Mode: " . ($isDryRun ? "DRY RUN (preview only)" : "⚠️  LIVE (files WILL be modified)") . "\n";
echo "Scope: ALL admin pages (including sub-pages)\n";
echo "========================================================\n\n";

if (!$isDryRun) {
    echo "⚠️  WARNING: This will modify 185+ files!\n";
    echo "Press ENTER to continue or CTRL+C to cancel...\n";
    fgets(STDIN);
}

// Base directory
$baseDir = 'resources/views/admin';

// Statistics
$stats = [
    'total_files' => 0,
    'scanned_files' => 0,
    'modified_files' => 0,
    'skipped_files' => 0,
    'total_replacements' => 0,
    'by_type' => [
        'input' => 0,
        'select' => 0,
        'textarea' => 0,
        'checkbox' => 0,
        'radio' => 0,
        'neo_classes' => 0,
        'padding' => 0,
        'dark_bg' => 0,
    ],
];

// Pattern definitions
$patterns = [
    // Neo-* classes (exact matches)
    [
        'type' => 'neo_classes',
        'pattern' => '/class="neo-input([^"]*)"/i',
        'replacement' => 'class="{{ FormStandards::input() }}"',
        'description' => 'neo-input → FormStandards::input()',
    ],
    [
        'type' => 'neo_classes',
        'pattern' => '/class="neo-select([^"]*)"/i',
        'replacement' => 'class="{{ FormStandards::select() }}"',
        'description' => 'neo-select → FormStandards::select()',
    ],
    [
        'type' => 'neo_classes',
        'pattern' => '/class="neo-textarea([^"]*)"/i',
        'replacement' => 'class="{{ FormStandards::textarea() }}"',
        'description' => 'neo-textarea → FormStandards::textarea()',
    ],
    [
        'type' => 'neo_classes',
        'pattern' => '/class="neo-checkbox([^"]*)"/i',
        'replacement' => 'class="{{ FormStandards::checkbox() }}"',
        'description' => 'neo-checkbox → FormStandards::checkbox()',
    ],
    [
        'type' => 'neo_classes',
        'pattern' => '/class="neo-radio([^"]*)"/i',
        'replacement' => 'class="{{ FormStandards::radio() }}"',
        'description' => 'neo-radio → FormStandards::radio()',
    ],
    
    // Padding inconsistencies (within class attributes)
    [
        'type' => 'padding',
        'pattern' => '/\bpx-3\s+py-2\b/',
        'replacement' => 'px-4 py-2.5',
        'description' => 'px-3 py-2 → px-4 py-2.5',
    ],
    [
        'type' => 'padding',
        'pattern' => '/\bpx-4\s+py-3\b/',
        'replacement' => 'px-4 py-2.5',
        'description' => 'px-4 py-3 → px-4 py-2.5',
    ],
    
    // Dark background fixes (only for form inputs)
    [
        'type' => 'dark_bg',
        'pattern' => '/(\bclass="[^"]*\b)(dark:bg-gray-900)(\b[^"]*")/',
        'replacement' => '$1dark:bg-gray-800$3',
        'description' => 'dark:bg-gray-900 → dark:bg-gray-800 (in form elements)',
        'conditional' => true, // Only if the class string contains form-related classes
    ],
];

// Scan all admin Blade files
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$modifiedFilesDetails = [];

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $stats['total_files']++;
        
        $filepath = $file->getPathname();
        $relativePath = str_replace('resources/views/admin/', '', $filepath);
        $content = file_get_contents($filepath);
        $originalContent = $content;
        $fileReplacements = [];
        
        // Apply each pattern
        foreach ($patterns as $patternData) {
            $matchCount = preg_match_all($patternData['pattern'], $content);
            
            if ($matchCount > 0) {
                // Special handling for conditional patterns
                if (isset($patternData['conditional']) && $patternData['conditional']) {
                    // Only replace dark:bg-gray-900 if it's in a form element context
                    if (preg_match('/\b(input|select|textarea)\b.*dark:bg-gray-900/', $content)) {
                        $content = preg_replace($patternData['pattern'], $patternData['replacement'], $content);
                        $fileReplacements[] = [
                            'type' => $patternData['type'],
                            'desc' => $patternData['description'],
                            'count' => $matchCount,
                        ];
                        $stats['by_type'][$patternData['type']] += $matchCount;
                    }
                } else {
                    $content = preg_replace($patternData['pattern'], $patternData['replacement'], $content);
                    $fileReplacements[] = [
                        'type' => $patternData['type'],
                        'desc' => $patternData['description'],
                        'count' => $matchCount,
                    ];
                    $stats['by_type'][$patternData['type']] += $matchCount;
                }
            }
        }
        
        $stats['scanned_files']++;
        
        if ($content !== $originalContent) {
            $stats['modified_files']++;
            $totalFileReplacements = array_sum(array_column($fileReplacements, 'count'));
            $stats['total_replacements'] += $totalFileReplacements;
            
            $modifiedFilesDetails[] = [
                'path' => $relativePath,
                'replacements' => $fileReplacements,
                'total' => $totalFileReplacements,
            ];
            
            if (!$isDryRun) {
                file_put_contents($filepath, $content);
                echo "✅ {$relativePath} ({$totalFileReplacements} changes)\n";
            } else {
                echo "📝 {$relativePath} ({$totalFileReplacements} changes - preview)\n";
                foreach ($fileReplacements as $rep) {
                    echo "   → {$rep['desc']} ({$rep['count']}x)\n";
                }
            }
        } else {
            $stats['skipped_files']++;
        }
    }
}

// Summary Report
echo "\n";
echo "========================================================\n";
echo "📊 MIGRATION SUMMARY\n";
echo "========================================================\n";
echo "Total files found: {$stats['total_files']}\n";
echo "Files scanned: {$stats['scanned_files']}\n";
echo "Files modified: {$stats['modified_files']}\n";
echo "Files skipped: {$stats['skipped_files']}\n";
echo "Total replacements: {$stats['total_replacements']}\n";
echo "========================================================\n\n";

echo "📈 BREAKDOWN BY TYPE:\n";
foreach ($stats['by_type'] as $type => $count) {
    if ($count > 0) {
        $label = str_pad(ucfirst(str_replace('_', ' ', $type)), 20);
        echo "  {$label}: {$count}\n";
    }
}
echo "\n";

if ($isDryRun && $stats['modified_files'] > 0) {
    echo "========================================================\n";
    echo "ℹ️  DRY RUN COMPLETE\n";
    echo "========================================================\n";
    echo "To apply these changes, run:\n";
    echo "  php scripts/comprehensive-form-migration.php\n\n";
} elseif (!$isDryRun && $stats['modified_files'] > 0) {
    echo "========================================================\n";
    echo "✅ MIGRATION COMPLETE\n";
    echo "========================================================\n";
    echo "Files modified: {$stats['modified_files']}\n";
    echo "Total changes: {$stats['total_replacements']}\n\n";
    
    echo "🔍 NEXT STEPS:\n";
    echo "1. Test all modified pages manually\n";
    echo "2. Check dark mode on modified forms\n";
    echo "3. Verify WCAG AAA contrast ratios\n";
    echo "4. Run: php artisan test (if you have tests)\n";
    echo "5. Commit changes with: git add . && git commit -m 'Migrate to FormStandards'\n\n";
}

if ($stats['modified_files'] === 0) {
    echo "========================================================\n";
    echo "✨ NO CHANGES NEEDED\n";
    echo "========================================================\n";
    echo "All files are already compliant with FormStandards!\n\n";
}

// Generate detailed report file
if (!$isDryRun && $stats['modified_files'] > 0) {
    $reportFile = 'migration-report-' . date('Y-m-d-His') . '.txt';
    $reportContent = "FORM STANDARDS MIGRATION REPORT\n";
    $reportContent .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $reportContent .= "SUMMARY:\n";
    $reportContent .= "Total files: {$stats['total_files']}\n";
    $reportContent .= "Modified files: {$stats['modified_files']}\n";
    $reportContent .= "Total replacements: {$stats['total_replacements']}\n\n";
    $reportContent .= "MODIFIED FILES:\n";
    foreach ($modifiedFilesDetails as $detail) {
        $reportContent .= "\n{$detail['path']} ({$detail['total']} changes):\n";
        foreach ($detail['replacements'] as $rep) {
            $reportContent .= "  - {$rep['desc']}: {$rep['count']}x\n";
        }
    }
    file_put_contents($reportFile, $reportContent);
    echo "📄 Detailed report saved: {$reportFile}\n\n";
}

