<?php
/**
 * Form Standards Migration Script - FULL MIGRATION
 *
 * Tüm admin Blade dosyalarındaki form elemanlarını FormStandards'a göre günceller.
 * Alt sayfalar, partials, modals, components dahil tüm dosyaları tarar.
 *
 * Usage:
 * php scripts/migrate-to-form-standards.php
 * php scripts/migrate-to-form-standards.php --dry-run
 *
 * @version 2.0.0
 * @since 2025-11-02
 */

$isDryRun = in_array('--dry-run', $argv);

echo "\n";
echo "========================================================\n";
echo "📋 FORM STANDARDS MIGRATION - FULL SYSTEM\n";
echo "========================================================\n";
echo "Mode: " . ($isDryRun ? "DRY RUN (preview only)" : "🔴 LIVE (files will be modified)") . "\n";
echo "Scope: ALL admin pages + subpages + partials + modals\n";
echo "========================================================\n\n";

// Scan ALL admin directories recursively
$baseDirectory = 'resources/views/admin';

// Statistics
$stats = [
    'total_files' => 0,
    'modified_files' => 0,
    'total_replacements' => 0,
    'by_type' => [
        'neo_classes' => 0,
        'padding_fixes' => 0,
        'dark_bg_fixes' => 0,
        'focus_ring_fixes' => 0,
    ],
    'by_directory' => []
];

function scanAndMigrate($directory, &$stats, $isDryRun) {
    if (!is_dir($directory)) {
        echo "⚠️  Directory not found: $directory\n";
        return;
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $stats['total_files']++;
            $filepath = $file->getPathname();
            $content = file_get_contents($filepath);
            $originalContent = $content;
            $fileReplacements = 0;
            $replacementDetails = [];

            // Pattern 1: Replace remaining neo-* classes
            $neoReplacements = [
                '/class="neo-input([^"]*)"/i' => function($matches) use (&$stats) {
                    $stats['by_type']['neo_classes']++;
                    return 'class="{{ FormStandards::input() }}"';
                },
                '/class="neo-select([^"]*)"/i' => function($matches) use (&$stats) {
                    $stats['by_type']['neo_classes']++;
                    return 'class="{{ FormStandards::select() }}"';
                },
                '/class="neo-textarea([^"]*)"/i' => function($matches) use (&$stats) {
                    $stats['by_type']['neo_classes']++;
                    return 'class="{{ FormStandards::textarea() }}"';
                },
                '/class="neo-checkbox([^"]*)"/i' => function($matches) use (&$stats) {
                    $stats['by_type']['neo_classes']++;
                    return 'class="{{ FormStandards::checkbox() }}"';
                },
                '/class="neo-radio([^"]*)"/i' => function($matches) use (&$stats) {
                    $stats['by_type']['neo_classes']++;
                    return 'class="{{ FormStandards::radio() }}"';
                },
            ];

            foreach ($neoReplacements as $pattern => $callback) {
                $content = preg_replace_callback($pattern, function($matches) use ($callback, &$replacementDetails) {
                    $replacementDetails[] = "neo-* class removed";
                    return $callback($matches);
                }, $content);
            }

            // Pattern 2: Fix padding inconsistencies (px-3 py-2 → px-4 py-2.5)
            if (preg_match('/px-3\s+py-2(?!\.)/', $content)) {
                $content = preg_replace('/px-3\s+py-2(?!\.)/', 'px-4 py-2.5', $content);
                $stats['by_type']['padding_fixes']++;
                $replacementDetails[] = "padding standardized";
            }

            // Pattern 3: Fix py-3 → py-2.5 (in form contexts)
            if (preg_match('/(?:input|select|textarea)[^>]*py-3/', $content)) {
                $content = preg_replace('/((?:input|select|textarea)[^>]*)py-3/', '$1py-2.5', $content);
                $stats['by_type']['padding_fixes']++;
                $replacementDetails[] = "py-3 fixed to py-2.5";
            }

            // Pattern 4: Fix dark:bg-gray-900 → dark:bg-gray-800 (for inputs only)
            if (preg_match('/<(?:input|select|textarea)[^>]*dark:bg-gray-900/', $content)) {
                $content = preg_replace('/(<(?:input|select|textarea)[^>]*)dark:bg-gray-900/', '$1dark:bg-gray-800', $content);
                $stats['by_type']['dark_bg_fixes']++;
                $replacementDetails[] = "dark background standardized";
            }

            // Pattern 5: Fix focus:ring-indigo-* → focus:ring-blue-*
            if (preg_match('/focus:ring-indigo-/', $content)) {
                $content = preg_replace('/focus:ring-indigo-(\d+)/', 'focus:ring-blue-$1', $content);
                $stats['by_type']['focus_ring_fixes']++;
                $replacementDetails[] = "focus ring color standardized";
            }

            if ($content !== $originalContent) {
                $stats['modified_files']++;
                $fileReplacements = count($replacementDetails);
                $stats['total_replacements'] += $fileReplacements;

                // Track by directory
                $relativeDir = dirname(str_replace('resources/views/admin/', '', $filepath));
                if (!isset($stats['by_directory'][$relativeDir])) {
                    $stats['by_directory'][$relativeDir] = 0;
                }
                $stats['by_directory'][$relativeDir]++;

                if (!$isDryRun) {
                    file_put_contents($filepath, $content);
                }

                $relPath = str_replace(getcwd() . '/', '', $filepath);
                echo "✅ $relPath\n";
                foreach ($replacementDetails as $detail) {
                    echo "   → $detail\n";
                }
            }
        }
    }
}

// Scan the entire admin directory
scanAndMigrate($baseDirectory, $stats, $isDryRun);

echo "\n";
echo "========================================================\n";
echo "📊 MIGRATION SUMMARY\n";
echo "========================================================\n";
echo "Total files scanned: {$stats['total_files']}\n";
echo "Files modified: {$stats['modified_files']}\n";
echo "Total replacements: {$stats['total_replacements']}\n";
echo "\n";
echo "By Type:\n";
echo "  - neo-* classes removed: {$stats['by_type']['neo_classes']}\n";
echo "  - Padding fixes: {$stats['by_type']['padding_fixes']}\n";
echo "  - Dark background fixes: {$stats['by_type']['dark_bg_fixes']}\n";
echo "  - Focus ring fixes: {$stats['by_type']['focus_ring_fixes']}\n";
echo "\n";

if (!empty($stats['by_directory'])) {
    echo "By Directory:\n";
    arsort($stats['by_directory']);
    foreach ($stats['by_directory'] as $dir => $count) {
        echo "  - $dir: $count files\n";
    }
    echo "\n";
}

echo "Mode: " . ($isDryRun ? "DRY RUN" : "🔴 LIVE") . "\n";
echo "========================================================\n\n";

if ($isDryRun && $stats['modified_files'] > 0) {
    echo "ℹ️  Run without --dry-run to apply changes:\n";
    echo "   php scripts/migrate-to-form-standards.php\n\n";
} else if (!$isDryRun && $stats['modified_files'] > 0) {
    echo "✅ Migration completed successfully!\n\n";
    echo "⚠️  NEXT STEPS:\n";
    echo "1. Clear view cache: php artisan view:clear\n";
    echo "2. Test pages in browser\n";
    echo "3. Check dark mode on all modified pages\n";
    echo "4. Verify WCAG AAA contrast\n\n";
} else {
    echo "✅ No files needed migration. All clean!\n\n";
}

echo "📚 See docs/FORM_STANDARDS.md for complete guide.\n\n";
