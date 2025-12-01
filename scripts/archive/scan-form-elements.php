#!/usr/bin/env php
<?php

/**
 * 🔍 FORM ELEMENT SCANNER
 *
 * Tüm .blade.php dosyalarındaki form elemanlarını tarar
 * Blade component'e dönüştürülebilir olanları tespit eder
 *
 * Context7 Compliance: ✅
 * Yalıhan Bekçi: Form Analysis Tool
 */
$stats = [
    'total_files' => 0,
    'files_with_forms' => 0,
    'total_inputs' => 0,
    'total_selects' => 0,
    'total_textareas' => 0,
    'convertible' => 0,
    'already_components' => 0,
];

$details = [];

// Scan resources/views directory
$directory = __DIR__.'/../resources/views';
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directory)
);

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "🔍 FORM ELEMENT SCANNER - STARTING\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }

    $stats['total_files']++;
    $filepath = $file->getPathname();
    $relativePath = str_replace(__DIR__.'/../', '', $filepath);
    $content = file_get_contents($filepath);

    // Count existing Blade components
    $componentCount = preg_match_all('/<x-form\.(input|select|textarea)/', $content, $componentMatches);
    if ($componentCount > 0) {
        $stats['already_components'] += $componentCount;
    }

    // Count raw HTML inputs (not inside <x-form.* components)
    $inputCount = preg_match_all('/<input(?![^>]*type="hidden")/', $content, $inputMatches);
    $selectCount = preg_match_all('/<select/', $content, $selectMatches);
    $textareaCount = preg_match_all('/<textarea/', $content, $textareaMatches);

    $totalElements = $inputCount + $selectCount + $textareaCount;

    if ($totalElements > 0) {
        $stats['files_with_forms']++;
        $stats['total_inputs'] += $inputCount;
        $stats['total_selects'] += $selectCount;
        $stats['total_textareas'] += $textareaCount;

        // Check if convertible (has class attribute and name)
        $convertibleInputs = preg_match_all(
            '/<input[^>]*class="[^"]*"[^>]*name="[^"]*"[^>]*>/',
            $content,
            $convMatches
        );
        $convertibleSelects = preg_match_all(
            '/<select[^>]*class="[^"]*"[^>]*name="[^"]*"[^>]*>/',
            $content,
            $convMatches
        );
        $convertibleTextareas = preg_match_all(
            '/<textarea[^>]*class="[^"]*"[^>]*name="[^"]*"[^>]*>/',
            $content,
            $convMatches
        );

        $convertible = $convertibleInputs + $convertibleSelects + $convertibleTextareas;
        $stats['convertible'] += $convertible;

        $details[] = [
            'file' => $relativePath,
            'inputs' => $inputCount,
            'selects' => $selectCount,
            'textareas' => $textareaCount,
            'total' => $totalElements,
            'convertible' => $convertible,
            'components' => $componentCount,
        ];
    }
}

// Sort by total elements (descending)
usort($details, fn ($a, $b) => $b['total'] <=> $a['total']);

// Display summary
echo "📊 SUMMARY STATISTICS:\n";
echo "─────────────────────────────────────────────────────────────\n";
echo sprintf("  Total Files Scanned:     %d\n", $stats['total_files']);
echo sprintf("  Files with Forms:        %d\n", $stats['files_with_forms']);
echo sprintf("  Already Using Components: %d ✅\n\n", $stats['already_components']);

echo sprintf("  Raw HTML Elements:\n");
echo sprintf("    <input>:               %d\n", $stats['total_inputs']);
echo sprintf("    <select>:              %d\n", $stats['total_selects']);
echo sprintf("    <textarea>:            %d\n", $stats['total_textareas']);
echo sprintf("    ────────────────────────────\n");
echo sprintf("    TOTAL RAW:             %d\n\n", $stats['total_inputs'] + $stats['total_selects'] + $stats['total_textareas']);

echo sprintf("  Convertible to Components: %d 🎯\n\n", $stats['convertible']);

// Display top 20 files
echo "═══════════════════════════════════════════════════════════════\n";
echo "📄 TOP 20 FILES WITH MOST FORM ELEMENTS:\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$displayLimit = min(20, count($details));
for ($i = 0; $i < $displayLimit; $i++) {
    $file = $details[$i];
    echo sprintf("%2d. %s\n", $i + 1, basename($file['file']));
    echo sprintf("    Path: %s\n", dirname($file['file']));
    echo sprintf("    Elements: %d input | %d select | %d textarea | %d components\n",
        $file['inputs'],
        $file['selects'],
        $file['textareas'],
        $file['components']
    );
    echo sprintf("    Convertible: %d/%d (%.0f%%)\n\n",
        $file['convertible'],
        $file['total'],
        $file['total'] > 0 ? ($file['convertible'] / $file['total']) * 100 : 0
    );
}

// Recommendations
echo "═══════════════════════════════════════════════════════════════\n";
echo "💡 RECOMMENDATIONS:\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$totalRaw = $stats['total_inputs'] + $stats['total_selects'] + $stats['total_textareas'];
$conversionRate = $totalRaw > 0 ? ($stats['convertible'] / $totalRaw) * 100 : 0;

if ($conversionRate > 80) {
    echo "✅ EXCELLENT: {$conversionRate}% of forms are convertible!\n";
    echo "   → Run auto-conversion script immediately\n\n";
} elseif ($conversionRate > 50) {
    echo "⚠️  GOOD: {$conversionRate}% of forms are convertible\n";
    echo "   → Run auto-conversion for convertible forms\n";
    echo "   → Manually fix remaining forms\n\n";
} else {
    echo "❌ WARNING: Only {$conversionRate}% are auto-convertible\n";
    echo "   → Manual review required for most forms\n\n";
}

echo "🎯 NEXT STEPS:\n\n";
echo "1. Review top files for conversion priority\n";
echo "2. Run: php scripts/convert-to-blade-components.php --dry-run\n";
echo "3. Verify changes and run without --dry-run\n";
echo "4. Test pages mentioned in issue:\n";
echo "   - /admin/talepler/create\n";
echo "   - /admin/ilan-kategorileri/1/edit\n";
echo "   - /admin/property-type-manager/1\n\n";

echo "═══════════════════════════════════════════════════════════════\n\n";
