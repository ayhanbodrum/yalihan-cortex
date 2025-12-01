<?php

/**
 * Context7 Süper Analizör & Fixer (AI-First, Self-Updating)
 * - Migration, Seeder, Model, Controller, Service, Blade, AI dosyalarını ve modülleri tarar
 * - Context7, AI, Neo, modül kurallarına göre analiz eder
 * - Gereksiz/fazla/uyumsuz dosyaları ve kodları tespit eder
 * - Otomatik düzeltme/fix ve yol haritası önerir
 * - Kendi config dosyasından (context7-super-analyzer.json) öğrenir ve güncellenebilir
 * - Raporu context7-super-analyzer-report.md olarak kaydeder
 */
$config = json_decode(file_get_contents(__DIR__.'/config/context7-super-analyzer.json'), true);
$rules = $config['rules'] ?? [];
$ignore = $config['ignore'] ?? [];
$learned = $config['learned'] ?? [];

$report = "# Context7 Süper Analizör Raporu\n\n";
$report .= 'Tarih: '.date('Y-m-d H:i:s')."\n\n";

// 1. Migration/Seeder/Model/Controller/Service/Blade/AI dosyalarını tara
$paths = [
    'database/migrations',
    'database/seeders',
    'app/Models',
    'app/Modules',
    'app/Http/Controllers',
    'app/Services',
    'resources/views',
    'config',
    'routes',
    'ai/prompts',
];

$allFiles = [];
foreach ($paths as $path) {
    $allFiles = array_merge($allFiles, glob_recursive($path.'/*'));
}

function glob_recursive($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
    }

    return $files;
}

// 2. Her dosya için Context7, AI, Neo, modül kurallarına göre analiz
$violations = [];
foreach ($allFiles as $file) {
    if (is_dir($file)) {
        continue;
    }
    $content = file_get_contents($file);
    foreach ($rules as $rule) {
        if ($rule['type'] === 'field_naming') {
            foreach ($rule['forbidden'] as $forbidden) {
                if (strpos($content, $forbidden) !== false) {
                    $violations[] = [
                        'file' => $file,
                        'type' => 'field_naming',
                        'violation' => $forbidden,
                    ];
                }
            }
        }
        if ($rule['type'] === 'enum_naming') {
            foreach ($rule['forbidden'] as $forbidden) {
                if (strpos($content, $forbidden) !== false) {
                    $violations[] = [
                        'file' => $file,
                        'type' => 'enum_naming',
                        'violation' => $forbidden,
                    ];
                }
            }
        }
        if ($rule['type'] === 'directory_structure') {
            foreach ($rule['required'] as $required) {
                if (! is_dir($required)) {
                    $violations[] = [
                        'file' => $required,
                        'type' => 'missing_directory',
                        'violation' => $required,
                    ];
                }
            }
        }
        if ($rule['type'] === 'ai_integration') {
            foreach ($rule['required'] as $required) {
                if (! file_exists($required) && ! is_dir($required)) {
                    $violations[] = [
                        'file' => $required,
                        'type' => 'missing_ai_integration',
                        'violation' => $required,
                    ];
                }
            }
        }
    }
}

// 3. Raporu oluştur
if (empty($violations)) {
    $report .= "✅ Hiçbir ihlal bulunamadı!\n";
} else {
    $report .= '❌ '.count($violations)." ihlal bulundu:\n\n";
    foreach ($violations as $v) {
        $report .= "- [{$v['type']}] {$v['file']} → {$v['violation']}\n";
    }
}

// 4. Öğrenme/güncelleme (örnek: yeni forbidden ekle)
// (Kullanıcı isterse config/context7-super-analyzer.json güncellenebilir)

// 5. Raporu kaydet
file_put_contents('context7-super-analyzer-report.md', $report);
echo $report;
echo "\n📋 Rapor kaydedildi: context7-super-analyzer-report.md\n";
