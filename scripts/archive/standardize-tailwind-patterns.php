#!/usr/bin/env php
<?php
/**
 * Tailwind Pattern Standardization Script
 * Yalıhan Bekçi - 2 Kasım 2025
 * 
 * Farklı Tailwind pattern'lerini tek standarda çevirir:
 * - px-3 py-2 → px-4 py-2.5
 * - rounded-md → rounded-lg
 * - focus:ring-indigo-500 → focus:ring-blue-500
 * - dark:bg-gray-700 → dark:bg-gray-800
 */

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🎨 TAILWIND PATTERN STANDARDIZATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

// Configuration
$dryRun = in_array('--dry-run', $argv);
$verbose = in_array('--verbose', $argv);

// Get path (skip script name and flags)
$path = 'resources/views';
foreach ($argv as $i => $arg) {
    if ($i > 0 && !str_starts_with($arg, '--')) {
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

echo "📁 Dosya sayısı: " . count($bladeFiles) . "\n\n";

// Statistics
$stats = [
    'files_checked' => 0,
    'files_modified' => 0,
    'padding_standardized' => 0,
    'border_radius_standardized' => 0,
    'focus_ring_standardized' => 0,
    'dark_bg_standardized' => 0,
];

// Replacement patterns
$replacements = [
    // Padding: px-3 py-2 → px-4 py-2.5
    '/\bpx-3\s+py-2\b/' => [
        'replacement' => 'px-4 py-2.5',
        'stat' => 'padding_standardized',
        'description' => 'Padding standardized (px-3 py-2 → px-4 py-2.5)',
    ],
    
    // Border Radius: rounded-md → rounded-lg
    '/\brounded-md\b/' => [
        'replacement' => 'rounded-lg',
        'stat' => 'border_radius_standardized',
        'description' => 'Border radius standardized (rounded-md → rounded-lg)',
    ],
    
    // Focus Ring: indigo → blue
    '/focus:ring-indigo-(\d+)/' => [
        'replacement' => 'focus:ring-blue-$1',
        'stat' => 'focus_ring_standardized',
        'description' => 'Focus ring color standardized (indigo → blue)',
    ],
    
    '/focus:border-indigo-(\d+)/' => [
        'replacement' => 'focus:border-blue-$1',
        'stat' => 'focus_ring_standardized',
        'description' => 'Focus border color standardized (indigo → blue)',
    ],
    
    // Dark Background: gray-700 → gray-800
    '/dark:bg-gray-700\b/' => [
        'replacement' => 'dark:bg-gray-800',
        'stat' => 'dark_bg_standardized',
        'description' => 'Dark background standardized (gray-700 → gray-800)',
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
                echo "  ✓ " . $config['description'] . " ({$matches}x)\n";
            }
        }
    }
    
    // Save changes
    if ($fileModified) {
        $stats['files_modified']++;
        
        if (!$dryRun) {
            file_put_contents($file, $content);
        }
        
        $relativePath = str_replace(getcwd() . '/', '', $file);
        echo "✅ " . $relativePath . "\n";
        
        if ($verbose) {
            echo "\n";
        }
    }
}

// Summary
echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 STANDARDIZATION SUMMARY\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

echo "Dosyalar:\n";
echo "  Kontrol edilen: {$stats['files_checked']}\n";
echo "  Değiştirilen: {$stats['files_modified']}\n";
echo "\n";

echo "Pattern Standardizations:\n";
echo "  px-3 py-2 → px-4 py-2.5: {$stats['padding_standardized']}\n";
echo "  rounded-md → rounded-lg: {$stats['border_radius_standardized']}\n";
echo "  indigo → blue (focus): {$stats['focus_ring_standardized']}\n";
echo "  dark:bg-gray-700 → gray-800: {$stats['dark_bg_standardized']}\n";
echo "\n";

$totalReplacements = 
    $stats['padding_standardized'] + 
    $stats['border_radius_standardized'] + 
    $stats['focus_ring_standardized'] + 
    $stats['dark_bg_standardized'];

echo "Toplam: {$totalReplacements} pattern standardized\n";
echo "\n";

if ($dryRun) {
    echo "🔍 DRY RUN tamamlandı - hiçbir değişiklik yapılmadı.\n";
    echo "   Gerçek standardization için: php scripts/standardize-tailwind-patterns.php\n";
} else {
    echo "✅ Standardization tamamlandı!\n";
    echo "\n";
    echo "Sonraki adımlar:\n";
    echo "  1. Browser'da test et\n";
    echo "  2. Tutarlı görünüm kontrol et\n";
    echo "  3. Dark mode kontrol et\n";
    echo "  4. git add -u && git commit -m 'refactor(forms): standardize Tailwind patterns'\n";
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

exit($dryRun ? 0 : 0);

