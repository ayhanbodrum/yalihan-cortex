<?php

/**
 * CSS Conflict Fixer - Blade template'lerinde Tailwind CSS çakışmalarını düzelten script
 */
echo "🎨 CSS Conflict Fixer - Tailwind Border Conflicts!\n";
echo "🔧 border-gray-300 ve @error border-red-500 çakışmalarını düzeltiyoruz...\n\n";

$viewFile = __DIR__.'/../resources/views/admin/ilanlar/stable-create.blade.php';
$content = file_get_contents($viewFile);
$fixedCount = 0;

// Pattern: border-gray-300 ile @error('field') border-red-500 çakışmaları
$patterns = [
    // Pattern 1: Standard border conflict
    '/class="([^"]*)\bborder-gray-300([^"]*)\s@error\([^)]+\)\s+border-red-500\s+@enderror([^"]*)"/i' => 'class="$1@error($2) border-red-500 @else border-gray-300 @enderror$3"',

    // Pattern 2: Dark mode ile birlikte
    '/class="([^"]*)\bborder-gray-300\s+dark:border-gray-600([^"]*)\s@error\([^)]+\)\s+border-red-500\s+@enderror([^"]*)"/i' => 'class="$1@error($2) border-red-500 @else border-gray-300 dark:border-gray-600 @enderror$3"',
];

foreach ($patterns as $pattern => $replacement) {
    $oldContent = $content;
    $content = preg_replace($pattern, $replacement, $content);
    $matches = $oldContent !== $content ? substr_count($content, '@error') - substr_count($oldContent, '@error') : 0;
    if ($matches > 0) {
        $fixedCount += $matches;
        echo "✅ Pattern fixed: $matches instances\n";
    }
}

// Manuel fixes for remaining conflicts - specific line patterns from error attachments
$manualFixes = [
    // Common input patterns
    'border border-gray-300 dark:border-gray-600' => '@error($field) border-red-500 @else border-gray-300 dark:border-gray-600 @enderror',
    'border-gray-300 @error' => '@error',
];

$originalContent = file_get_contents($viewFile);

// Generic fix for all input fields with border conflicts
$content = preg_replace_callback(
    '/class="([^"]*border[^"]*@error\([\'"]([^\'"]+)[\'"]\)[^"]*border-red-500[^"]*@enderror[^"]*)"/i',
    function ($matches) {
        $fullClass = $matches[1];
        $fieldName = $matches[2];

        // Remove conflicting border classes and rebuild conditionally
        $cleanClass = preg_replace('/\bborder-gray-300\b/', '', $fullClass);
        $cleanClass = preg_replace('/\bdark:border-gray-600\b/', '', $cleanClass);
        $cleanClass = preg_replace('/@error\([\'"][^\'"]+[\'"]\)\s+border-red-500\s+@enderror/', '', $cleanClass);

        // Rebuild with proper conditional
        $newClass = trim($cleanClass)." @error('$fieldName') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror";

        return 'class="'.preg_replace('/\s+/', ' ', $newClass).'"';
    },
    $content
);

if ($content !== $originalContent) {
    if (file_put_contents($viewFile, $content)) {
        echo "✅ Blade template updated successfully!\n";
        $fixedCount++;
    } else {
        echo "❌ Failed to write file\n";
    }
} else {
    echo "⏭️ No additional changes needed\n";
}

echo "\n📊 CSS Conflict Fixer Özeti:\n";
echo "🎨 Düzeltilen çakışmalar: $fixedCount\n";
echo "📁 Dosya: stable-create.blade.php\n";

// Count remaining warnings
$remainingConflicts = substr_count(file_get_contents($viewFile), 'border-gray-300') + substr_count(file_get_contents($viewFile), 'border-red-500');
echo '⚠️ Kalan potansiyel çakışmalar: '.($remainingConflicts > 0 ? 'Kontrol edilecek' : 'Temiz')."\n";

echo "\n🎨 CSS Conflict Fixer tamamlandı!\n";
