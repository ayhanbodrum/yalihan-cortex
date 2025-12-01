<?php

/**
 * Schema Callback Closure Fixer - Bu spesifik pattern'ı çözen targeted fixer
 */
echo "🎯 Schema Callback Closure Fixer - Spesifik Pattern!\n";
echo "🔧 'unexpected token public, expecting )' hatalarını çözüyoruz...\n\n";

$migrationsDir = __DIR__.'/../database/migrations';
$fixedCount = 0;

foreach (glob($migrationsDir.'/*.php') as $filePath) {
    $filename = basename($filePath);

    $syntaxCheck = shell_exec('php -l '.escapeshellarg($filePath).' 2>&1');
    if (strpos($syntaxCheck, 'No syntax errors') !== false) {
        continue;
    }

    // Sadece "unexpected token 'public', expecting ')'" hatalarını ele al
    if (strpos($syntaxCheck, 'unexpected token "public", expecting ")"') === false) {
        continue;
    }

    echo "🔧 $filename -> ";

    $content = file_get_contents($filePath);
    $lines = explode("\n", $content);
    $modified = false;

    for ($i = 0; $i < count($lines); $i++) {
        $line = trim($lines[$i]);

        // Schema::create veya Schema::table callback açılışı tespit et
        if (preg_match('/Schema::(create|table)\([^)]+function[^{]*\{/', $line)) {

            // Bu callback'in kapanış satırını ara
            $bracketCount = 1; // Opening bracket

            for ($j = $i + 1; $j < count($lines); $j++) {
                $nextLine = trim($lines[$j]);

                // Bracket counting
                $bracketCount += substr_count($nextLine, '{');
                $bracketCount -= substr_count($nextLine, '}');

                // Eğer bracket count 0'a düştü ve bu satır sadece } ise
                if ($bracketCount === 0 && $nextLine === '}') {
                    // Bu Schema callback kapanışı, });\n} olmalı
                    $lines[$j] = '        });';

                    // Sonraki satırda up() fonksiyonunu kapat
                    if ($j + 1 < count($lines) && trim($lines[$j + 1]) === '') {
                        $lines[$j + 1] = '    }';
                    } elseif ($j + 1 < count($lines)) {
                        array_splice($lines, $j + 1, 0, ['    }']);
                    }

                    $modified = true;
                    break;
                }

                // Eğer public function görüyoruz ve bracket hala açıksa
                if (strpos($nextLine, 'public function') !== false && $bracketCount > 0) {
                    // Schema callback kapanmamış, önceki satırda kapat
                    if ($j > 0) {
                        $prevLine = trim($lines[$j - 1]);

                        if ($prevLine === '}') {
                            $lines[$j - 1] = '        });';
                            array_splice($lines, $j, 0, ['    }', '']);
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

    if ($modified) {
        $newContent = implode("\n", $lines);

        if (file_put_contents($filePath, $newContent)) {
            $fixedCount++;

            // Verification
            $verifyCheck = shell_exec('php -l '.escapeshellarg($filePath).' 2>&1');
            if (strpos($verifyCheck, 'No syntax errors') !== false) {
                echo "✅ SUCCESS!\n";
            } else {
                echo "⚠️ Fixed but other errors remain\n";
            }
        } else {
            echo "❌ Write failed\n";
        }
    } else {
        echo "⏭️ No fix applied\n";
    }
}

echo "\n📊 Schema Callback Fixer Özeti:\n";
echo "🔧 Düzeltilen dosyalar: $fixedCount\n";

// Error count check
$currentErrors = (int) shell_exec('find '.escapeshellarg($migrationsDir)." -name '*.php' -exec php -l {} \\; 2>&1 | grep -c 'Parse error\\|Fatal error\\|syntax error' || echo '0'");
$improvement = 197 - $currentErrors;

echo "⚠️ Önceki hata sayısı: 197\n";
echo "⚠️ Güncel hata sayısı: $currentErrors\n";
echo "📈 İyileştirme: $improvement hata çözüldü\n";

if ($currentErrors === 0) {
    echo "\n🎉🎉🎉 TÜM SYNTAX HATALARI ÇÖZÜLDÜ! 🎉🎉🎉\n";
} elseif ($improvement > 0) {
    echo "\n✅ İlerleme var! Devam ediyoruz...\n";
}

echo "\n🎯 Schema Callback Fixer tamamlandı!\n";
