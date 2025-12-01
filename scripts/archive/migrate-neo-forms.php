#!/usr/bin/env php
<?php

/**
 * Neo Form Migration Script
 * Yalıhan Bekçi - 2 Kasım 2025
 *
 * Converts neo-input classes to Tailwind standard forms
 * Fixes: Unreadable text, low contrast, accessibility issues
 */
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔧 FORM STANDARDIZATION MIGRATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

// Configuration
$dryRun = in_array('--dry-run', $argv);
$verbose = in_array('--verbose', $argv);

// Get path (skip script name and flags)
$path = 'resources/views';
foreach ($argv as $i => $arg) {
    if ($i > 0 && ! str_starts_with($arg, '--')) {
        $path = $arg;
        break;
    }
}

if ($dryRun) {
    echo "🔍 DRY RUN MODE (değişiklik yapılmayacak)\n\n";
}

// Find all Blade files
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($path)
);

$bladeFiles = [];
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $bladeFiles[] = $file->getPathname();
    }
}

echo '📁 Dosya sayısı: '.count($bladeFiles)."\n\n";

// Statistics
$stats = [
    'files_checked' => 0,
    'files_modified' => 0,
    'neo_input_replaced' => 0,
    'neo_select_replaced' => 0,
    'neo_textarea_replaced' => 0,
    'neo_checkbox_replaced' => 0,
    'inline_styles_removed' => 0,
];

// Replacement patterns
$replacements = [
    // Neo Input → Tailwind (with extra classes)
    '/class="neo-input([^"]*)"/' => [
        'replacement' => 'class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200$1"',
        'stat' => 'neo_input_replaced',
        'description' => 'neo-input → Tailwind standard input',
    ],

    // Neo Select → Tailwind (with extra classes)
    '/class="neo-select([^"]*)"/' => [
        'replacement' => 'class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 cursor-pointer appearance-none$1"',
        'stat' => 'neo_select_replaced',
        'description' => 'neo-select → Tailwind standard select',
    ],

    // Neo Textarea → Tailwind (with extra classes)
    '/class="neo-textarea([^"]*)"/' => [
        'replacement' => 'class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 resize-vertical$1"',
        'stat' => 'neo_textarea_replaced',
        'description' => 'neo-textarea → Tailwind standard textarea',
    ],

    // Neo Checkbox → Tailwind (with extra classes)
    '/class="neo-checkbox([^"]*)"/' => [
        'replacement' => 'class="w-5 h-5 text-blue-600 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 cursor-pointer$1"',
        'stat' => 'neo_checkbox_replaced',
        'description' => 'neo-checkbox → Tailwind standard checkbox',
    ],
];

// Process each file
foreach ($bladeFiles as $file) {
    $stats['files_checked']++;

    $content = file_get_contents($file);
    $originalContent = $content;
    $fileModified = false;

    // Apply replacements
    foreach ($replacements as $pattern => $config) {
        $matches = preg_match_all($pattern, $content);

        if ($matches > 0) {
            $content = preg_replace($pattern, $config['replacement'], $content);
            $stats[$config['stat']] += $matches;
            $fileModified = true;

            if ($verbose) {
                echo '  ✓ '.$config['description']." ({$matches}x)\n";
            }
        }
    }

    // Remove inline styles (basic detection)
    if (preg_match('/style="[^"]*"/', $content)) {
        $inlineStyleCount = preg_match_all('/style="[^"]*"/', $content);
        if ($verbose) {
            echo "  ⚠️  Inline style bulundu ({$inlineStyleCount}x) - manuel kontrol gerekli\n";
        }
        $stats['inline_styles_removed'] += $inlineStyleCount;
    }

    // Save changes
    if ($fileModified) {
        $stats['files_modified']++;

        if (! $dryRun) {
            file_put_contents($file, $content);
        }

        $relativePath = str_replace(getcwd().'/', '', $file);
        echo '✅ '.$relativePath."\n";

        if ($verbose) {
            echo "\n";
        }
    }
}

// Summary
echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 MIGRATION SUMMARY\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

echo "Dosyalar:\n";
echo "  Kontrol edilen: {$stats['files_checked']}\n";
echo "  Değiştirilen: {$stats['files_modified']}\n";
echo "\n";

echo "Replacements:\n";
echo "  neo-input → Tailwind: {$stats['neo_input_replaced']}\n";
echo "  neo-select → Tailwind: {$stats['neo_select_replaced']}\n";
echo "  neo-textarea → Tailwind: {$stats['neo_textarea_replaced']}\n";
echo "  neo-checkbox → Tailwind: {$stats['neo_checkbox_replaced']}\n";
echo "\n";

if ($stats['inline_styles_removed'] > 0) {
    echo "⚠️  Warnings:\n";
    echo "  Inline styles bulundu: {$stats['inline_styles_removed']}\n";
    echo "  Manuel kontrol gerekli!\n";
    echo "\n";
}

$totalReplacements =
    $stats['neo_input_replaced'] +
    $stats['neo_select_replaced'] +
    $stats['neo_textarea_replaced'] +
    $stats['neo_checkbox_replaced'];

echo "Toplam: {$totalReplacements} replacement\n";
echo "\n";

if ($dryRun) {
    echo "🔍 DRY RUN tamamlandı - hiçbir değişiklik yapılmadı.\n";
    echo "   Gerçek migration için: php scripts/migrate-neo-forms.php\n";
} else {
    echo "✅ Migration tamamlandı!\n";
    echo "\n";
    echo "Sonraki adımlar:\n";
    echo "  1. Browser'da test et\n";
    echo "  2. Dark mode kontrol et\n";
    echo "  3. Mobile responsive test et\n";
    echo "  4. Accessibility test et (Lighthouse)\n";
    echo "  5. git add -u && git commit -m 'feat(forms): migrate neo-input to Tailwind standards'\n";
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

exit($dryRun ? 0 : 0);
