#!/usr/bin/env php
<?php
/**
 * 🔄 BLADE COMPONENT CONVERTER
 *
 * Raw HTML form elemanlarını Blade component'lere dönüştürür
 *
 * Usage:
 *   php convert-to-blade-components.php --dry-run    # Preview only
 *   php convert-to-blade-components.php              # Apply changes
 *   php convert-to-blade-components.php --file=path  # Single file
 *
 * Context7 Compliance: ✅
 * Yalıhan Bekçi: Smart Form Converter
 */

$dryRun = in_array('--dry-run', $argv);
$singleFile = null;

foreach ($argv as $arg) {
    if (strpos($arg, '--file=') === 0) {
        $singleFile = substr($arg, 7);
    }
}

$stats = [
    'files_processed' => 0,
    'inputs_converted' => 0,
    'selects_converted' => 0,
    'textareas_converted' => 0,
    'failed' => 0,
    'skipped_complex' => 0,
];

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "🔄 BLADE COMPONENT CONVERTER\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

if ($dryRun) {
    echo "⚠️  DRY-RUN MODE: No files will be modified\n\n";
}

// Priority files (user requested)
$priorityFiles = [
    'resources/views/admin/talepler/create.blade.php',
    'resources/views/admin/ilan-kategorileri/edit.blade.php',
    'resources/views/admin/property-type-manager/edit.blade.php',
];

// Target directory
$directory = __DIR__ . '/../resources/views';

if ($singleFile) {
    $files = [$singleFile];
    echo "🎯 Processing single file: {$singleFile}\n\n";
} else {
    // Collect all blade files
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory)
    );

    $files = [];
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
}

foreach ($files as $filepath) {
    if (!file_exists($filepath)) {
        continue;
    }

    $relativePath = str_replace(__DIR__ . '/../', '', $filepath);
    $content = file_get_contents($filepath);
    $originalContent = $content;
    $changed = false;

    // PATTERN 1: Simple Text Inputs
    // <input type="text" name="name" class="..." value="{{ old('name') }}">
    // → <x-form.input name="name" :value="old('name')" />

    $pattern = '/<input\s+([^>]*?)type="text"([^>]*?)>/s';
    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $fullTag = $match[0];
        $attrs = $match[1] . $match[2];

        // Extract attributes
        $name = extractAttribute($attrs, 'name');
        $value = extractAttribute($attrs, 'value');
        $placeholder = extractAttribute($attrs, 'placeholder');
        $required = strpos($attrs, 'required') !== false;
        $id = extractAttribute($attrs, 'id');

        if (!$name) {
            $stats['skipped_complex']++;
            continue; // Skip if no name
        }

        // Look for label before input
        $labelPattern = '/<label[^>]*for=["\']?' . preg_quote($id ?: $name, '/') . '["\']?[^>]*>(.*?)<\/label>/s';
        preg_match($labelPattern, $content, $labelMatch);
        $label = $labelMatch ? strip_tags($labelMatch[1]) : ucfirst(str_replace('_', ' ', $name));
        $label = trim(str_replace('*', '', $label));

        // Build component
        $component = '<x-form.input';
        $component .= "\n    name=\"{$name}\"";
        $component .= "\n    label=\"{$label}\"";

        if ($value) {
            // Handle Laravel expressions
            if (strpos($value, '{{') !== false || strpos($value, '{!!') !== false) {
                $cleanValue = trim(str_replace(['{{', '}}', '{!!', '!!}'], '', $value));
                $component .= "\n    :value=\"{$cleanValue}\"";
            } else {
                $component .= "\n    value=\"{$value}\"";
            }
        }

        if ($placeholder) {
            $component .= "\n    placeholder=\"{$placeholder}\"";
        }

        if ($required) {
            $component .= "\n    required";
        }

        $component .= "\n/>";

        // Only convert if we have a name
        if ($name && !isComplexInput($attrs)) {
            $content = str_replace($fullTag, $component, $content);
            $stats['inputs_converted']++;
            $changed = true;

            // Remove the associated label if exists
            if ($labelMatch) {
                $content = str_replace($labelMatch[0], '', $content);
            }
        } else {
            $stats['skipped_complex']++;
        }
    }

    // PATTERN 2: Select Dropdowns
    $selectPattern = '/<select\s+([^>]*?)>(.*?)<\/select>/s';
    preg_match_all($selectPattern, $content, $selectMatches, PREG_SET_ORDER);

    foreach ($selectMatches as $match) {
        $fullTag = $match[0];
        $attrs = $match[1];
        $options = $match[2];

        $name = extractAttribute($attrs, 'name');
        $id = extractAttribute($attrs, 'id');
        $required = strpos($attrs, 'required') !== false;

        if (!$name || isComplexSelect($options)) {
            $stats['skipped_complex']++;
            continue;
        }

        // Look for label
        $labelPattern = '/<label[^>]*for=["\']?' . preg_quote($id ?: $name, '/') . '["\']?[^>]*>(.*?)<\/label>/s';
        preg_match($labelPattern, $content, $labelMatch);
        $label = $labelMatch ? strip_tags($labelMatch[1]) : ucfirst(str_replace('_', ' ', $name));
        $label = trim(str_replace('*', '', $label));

        // Build component
        $component = '<x-form.select';
        $component .= "\n    name=\"{$name}\"";
        $component .= "\n    label=\"{$label}\"";

        if ($required) {
            $component .= "\n    required";
        }

        $component .= "\n>";
        $component .= "\n    {$options}";
        $component .= "\n</x-form.select>";

        $content = str_replace($fullTag, $component, $content);
        $stats['selects_converted']++;
        $changed = true;

        if ($labelMatch) {
            $content = str_replace($labelMatch[0], '', $content);
        }
    }

    // PATTERN 3: Textareas
    $textareaPattern = '/<textarea\s+([^>]*?)>(.*?)<\/textarea>/s';
    preg_match_all($textareaPattern, $content, $textareaMatches, PREG_SET_ORDER);

    foreach ($textareaMatches as $match) {
        $fullTag = $match[0];
        $attrs = $match[1];
        $value = $match[2];

        $name = extractAttribute($attrs, 'name');
        $id = extractAttribute($attrs, 'id');
        $rows = extractAttribute($attrs, 'rows');
        $required = strpos($attrs, 'required') !== false;

        if (!$name) {
            $stats['skipped_complex']++;
            continue;
        }

        // Look for label
        $labelPattern = '/<label[^>]*for=["\']?' . preg_quote($id ?: $name, '/') . '["\']?[^>]*>(.*?)<\/label>/s';
        preg_match($labelPattern, $content, $labelMatch);
        $label = $labelMatch ? strip_tags($labelMatch[1]) : ucfirst(str_replace('_', ' ', $name));
        $label = trim(str_replace('*', '', $label));

        // Build component
        $component = '<x-form.textarea';
        $component .= "\n    name=\"{$name}\"";
        $component .= "\n    label=\"{$label}\"";

        if ($rows) {
            $component .= "\n    rows=\"{$rows}\"";
        }

        if ($required) {
            $component .= "\n    required";
        }

        $component .= "\n>";
        $component .= $value;
        $component .= "</x-form.textarea>";

        $content = str_replace($fullTag, $component, $content);
        $stats['textareas_converted']++;
        $changed = true;

        if ($labelMatch) {
            $content = str_replace($labelMatch[0], '', $content);
        }
    }

    // Save if changed
    if ($changed) {
        $stats['files_processed']++;

        if (!$dryRun) {
            file_put_contents($filepath, $content);
            echo "✅ {$relativePath}\n";
        } else {
            echo "📝 {$relativePath} (would be modified)\n";

            // Show diff sample
            if ($stats['files_processed'] <= 3) {
                echo "   Sample change:\n";
                $diffLines = array_slice(explode("\n", $content), 0, 10);
                foreach ($diffLines as $line) {
                    if (strpos($line, '<x-form.') !== false) {
                        echo "   + " . trim($line) . "\n";
                        break;
                    }
                }
                echo "\n";
            }
        }
    }
}

// Helper functions
function extractAttribute($attrs, $name) {
    $pattern = '/' . preg_quote($name, '/') . '=["\']([^"\']*)["\']?/';
    preg_match($pattern, $attrs, $match);
    return $match[1] ?? null;
}

function isComplexInput($attrs) {
    // Skip if has Alpine.js bindings, multiple classes with logic, etc.
    if (strpos($attrs, 'x-') !== false || strpos($attrs, '@') !== false) {
        return true;
    }
    if (strpos($attrs, 'wire:') !== false) {
        return true;
    }
    return false;
}

function isComplexSelect($options) {
    // Skip if has @foreach or complex logic
    if (strpos($options, '@foreach') !== false || strpos($options, '@forelse') !== false) {
        return true;
    }
    if (strpos($options, 'x-') !== false) {
        return true;
    }
    return false;
}

// Display results
echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "📊 CONVERSION SUMMARY\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo sprintf("  Files Modified:        %d\n", $stats['files_processed']);
echo sprintf("  Inputs Converted:      %d\n", $stats['inputs_converted']);
echo sprintf("  Selects Converted:     %d\n", $stats['selects_converted']);
echo sprintf("  Textareas Converted:   %d\n", $stats['textareas_converted']);
echo sprintf("  ────────────────────────────\n");
echo sprintf("  TOTAL CONVERSIONS:     %d\n\n",
    $stats['inputs_converted'] + $stats['selects_converted'] + $stats['textareas_converted']
);
echo sprintf("  Complex (Skipped):     %d\n\n", $stats['skipped_complex']);

if ($dryRun) {
    echo "⚠️  DRY-RUN MODE: No files were actually modified\n";
    echo "   Run without --dry-run to apply changes\n\n";
} else {
    echo "✅ CONVERSION COMPLETE!\n\n";
}

echo "🎯 NEXT STEPS:\n\n";
echo "1. Clear cache: php artisan view:clear\n";
echo "2. Test priority pages:\n";
echo "   - /admin/talepler/create\n";
echo "   - /admin/ilan-kategorileri/1/edit\n";
echo "   - /admin/property-type-manager/1\n";
echo "3. Verify form styling and functionality\n";
echo "4. Manually fix complex forms that were skipped\n\n";

echo "═══════════════════════════════════════════════════════════════\n\n";
