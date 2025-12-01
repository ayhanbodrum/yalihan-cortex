<?php

/**
 * Final Push Fixer - Az kalan syntax hatalarını pattern recognition ile çözen son sistem
 */
echo "🚀 Final Push Fixer - Automated Learning Son Atak!\n";
echo "🎯 Kalan callback closure hatalarını çözüyoruz...\n\n";

$migrationsDir = __DIR__.'/../database/migrations';
$fixedCount = 0;

// Pattern: "unexpected token 'public', expecting ')'"
// Bu Schema callback'lerinde } yerine }) olması gereken durumlar

foreach (glob($migrationsDir.'/*.php') as $filePath) {
    $filename = basename($filePath);

    $syntaxCheck = shell_exec('php -l '.escapeshellarg($filePath).' 2>&1');
    if (strpos($syntaxCheck, 'No syntax errors') !== false) {
        continue;
    }

    // Sadece bu spesifik pattern'e odaklan
    if (strpos($syntaxCheck, 'unexpected token "public", expecting ")"') === false) {
        continue;
    }

    echo "🔧 $filename -> ";

    $content = file_get_contents($filePath);
    $lines = explode("\n", $content);
    $modified = false;

    // Schema callback patterns
    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];

        // Schema::create veya Schema::table callback açılışı tespit et
        if (preg_match('/Schema::(create|table)\([^)]+function[^{]*\{/', $line)) {

            // Callback içindeki satırları ara
            for ($j = $i + 1; $j < count($lines); $j++) {
                $nextLine = trim($lines[$j]);

                // Eğer public function görüyoruz ve callback kapanmamışsa
                if (strpos($nextLine, 'public function') !== false) {
                    // Önceki satırda callback kapanışı ekle
                    if ($j > 0) {
                        $prevLine = trim($lines[$j - 1]);

                        // Eğer önceki satır sadece } ile bitiyorsa, }); yap
                        if ($prevLine === '}') {
                            $lines[$j - 1] = '        });';
                            $modified = true;
                            break;
                        }
                        // Eğer semicolon ile bitiyorsa callback kapat
                        elseif (preg_match('/;$/', $prevLine)) {
                            array_splice($lines, $j, 0, ['        });']);
                            $modified = true;
                            break;
                        }
                    }
                }
            }

            if ($modified) {
                break;
            }
        }
    }

    // Genel pattern fix - callback closures
    if (! $modified) {
        $content = preg_replace(
            '/(\$table->[^;]+;)\s+(public function)/',
            '$1'."\n        });\n    }\n\n    $2",
            $content
        );

        if ($content !== file_get_contents($filePath)) {
            $modified = true;
        }
    }

    if ($modified) {
        $newContent = ($modified && isset($lines)) ? implode("\n", $lines) : $content;

        if (file_put_contents($filePath, $newContent)) {
            $fixedCount++;
            echo "✅ FIXED\n";
        } else {
            echo "❌ ERROR\n";
        }
    } else {
        echo "⏭️ No pattern match\n";
    }
}

echo "\n📊 Final Push Fixer Özeti:\n";
echo "🔧 Pattern-based fixes: $fixedCount dosya\n";

// Final count
$finalErrors = (int) shell_exec('find '.escapeshellarg($migrationsDir)." -name '*.php' -exec php -l {} \\; 2>&1 | grep -c 'Parse error\\|Fatal error\\|syntax error' || echo '0'");
$improvement = 197 - $finalErrors;

echo "⚠️ Önceki hatalar: 197\n";
echo "⚠️ Kalan hatalar: $finalErrors\n";
echo "📈 Bu push'ta düzeltilen: $improvement\n";

if ($finalErrors === 0) {
    echo "\n🎉🎉🎉 TÜM SYNTAX HATALARI ÇÖZÜLDÜ! 🎉🎉🎉\n";
    echo "🚀 AUTOMATED LEARNING SİSTEMİ BAŞARIYLA TAMAMLANDI!\n";
} elseif ($improvement > 0) {
    echo "\n✅ İlerleme kaydediliyor! Az kaldı...\n";
}

echo "\n🚀 Final Push Fixer tamamlandı!\n";
