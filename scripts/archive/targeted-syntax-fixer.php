<?php

/**
 * Targeted Syntax Fixer - Spesifik syntax hatalarını hedef alarak düzelten script
 */

$migrationsDir = __DIR__ . '/../database/migrations';
$fixedFiles = [];
$errorFiles = [];

echo "🎯 Targeted Syntax Fixer başlatılıyor...\n";

foreach (glob($migrationsDir . '/*.php') as $filePath) {
    $filename = basename($filePath);

    // İlk önce syntax kontrolü yaparak hatalı dosyaları tespit et
    $syntaxCheck = shell_exec("php -l " . escapeshellarg($filePath) . " 2>&1");
    if (strpos($syntaxCheck, 'No syntax errors') !== false) {
        continue; // Bu dosya zaten temiz
    }

    echo "🔧 Düzeltiliyor: $filename\n";

    $content = file_get_contents($filePath);
    $originalContent = $content;
    $lines = explode("\n", $content);

    // Problem 1: "unexpected token ';', expecting 'function'" - Line 19/22
    if (preg_match('/unexpected token ";", expecting "function"/', $syntaxCheck)) {
        for ($i = 15; $i < min(25, count($lines)); $i++) {
            // Yanlış: }; beklenen: } veya function
            if (trim($lines[$i]) === '};' && $i < count($lines) - 3) {
                // Eğer bu satırdan sonra 'public function down' geliyorsa
                if (isset($lines[$i+1]) && strpos($lines[$i+1], 'public function down') !== false) {
                    $lines[$i] = '    }'; // Sadece } yap
                    echo "  ✅ Line " . ($i+1) . ": '}; -> }' düzeltildi\n";
                }
                // Eğer bu class'ın gerçek sonuysa
                elseif ($i > count($lines) - 5) {
                    // En son satır olarak bırak
                }
                // Ortada bir yerdeyse ve sonrasında function geliyorsa
                else {
                    $lines[$i] = '    }';
                    echo "  ✅ Line " . ($i+1) . ": Intermediate '}; -> }' düzeltildi\n";
                }
            }
        }
    }

    // Problem 2: "Unmatched '}'" - Genellikle line 18-20
    if (preg_match('/Unmatched \'}\' in .* on line (\d+)/', $syntaxCheck, $matches)) {
        $errorLine = (int)$matches[1] - 1; // 0-indexed

        if ($errorLine > 0 && $errorLine < count($lines)) {
            $problematicLine = trim($lines[$errorLine]);

            // Eğer sadece '}' ise ve up() function'ın kapanışı eksikse
            if ($problematicLine === '}') {
                // Önceki satırları kontrol et
                $foundUpFunction = false;
                $foundDownFunction = false;

                for ($j = max(0, $errorLine - 10); $j < $errorLine; $j++) {
                    if (strpos($lines[$j], 'public function up') !== false) {
                        $foundUpFunction = true;
                    }
                    if (strpos($lines[$j], 'public function down') !== false) {
                        $foundDownFunction = true;
                    }
                }

                // up() function var ama down() yok, bu line down()'ın başlangıcı olmalı
                if ($foundUpFunction && !$foundDownFunction) {
                    $lines[$errorLine] = '    }' . "\n\n" . '    public function down(): void' . "\n" . '    {' . "\n" . '        //';
                    echo "  ✅ Line " . ($errorLine+1) . ": Missing down() function added\n";
                }
                // down() function da var, bu class'ın kapanışı olmalı
                elseif ($foundUpFunction && $foundDownFunction) {
                    // Son satırda ise class kapanışı
                    if ($errorLine > count($lines) - 4) {
                        $lines[$errorLine] = '    }' . "\n" . '};';
                        echo "  ✅ Line " . ($errorLine+1) . ": Class closing fixed\n";
                    }
                }
            }
        }
    }

    // Problem 3: Eksik down() function structure
    $newContent = implode("\n", $lines);

    // down() function eksikse ekle
    if (strpos($newContent, 'public function up') !== false && strpos($newContent, 'public function down') === false) {
        $newContent = preg_replace(
            '/(.*public function up\(\)[^}]*\})/s',
            '$1' . "\n\n" . '    public function down(): void' . "\n" . '    {' . "\n" . '        //' . "\n" . '    }',
            $newContent
        );
        echo "  ✅ Missing down() function added\n";
    }

    // Problem 4: Class ending düzeltme
    if (!preg_match('/\};\s*$/', $newContent) && strpos($newContent, 'return new class extends Migration') !== false) {
        $newContent = rtrim($newContent);
        if (!preg_match('/\}\s*$/', $newContent)) {
            $newContent .= "\n    }\n};";
        } else {
            $newContent .= "\n};";
        }
        echo "  ✅ Class ending fixed\n";
    }

    if ($newContent !== $originalContent) {
        if (file_put_contents($filePath, $newContent)) {
            $fixedFiles[] = $filename;
            echo "✅ Başarıyla düzeltildi: $filename\n";
        } else {
            $errorFiles[] = $filename;
            echo "❌ Hata: $filename\n";
        }
    }
}

echo "\n📊 Targeted Syntax Fixer Özeti:\n";
echo "✅ Düzeltilen dosyalar: " . count($fixedFiles) . "\n";
echo "❌ Hata alan dosyalar: " . count($errorFiles) . "\n";

if (!empty($fixedFiles)) {
    echo "\n🔧 Düzeltilen dosyalar:\n";
    foreach ($fixedFiles as $file) {
        echo "  - $file\n";
    }
}

echo "\n🎯 Targeted Syntax Fixer tamamlandı!\n";
