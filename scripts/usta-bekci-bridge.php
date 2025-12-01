<?php

/**
 * USTA → Yalıhan Bekçi Bridge
 *
 * USTA öğrenilen pattern'leri Yalıhan Bekçi'ye aktarır
 * Context7 Standardı: C7-USTA-BEKCI-BRIDGE-2025-11-26
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

$ustaPatternsFile = base_path('config/usta-learned-patterns.json');
$bekciKnowledgeBase = base_path('yalihan-bekci/knowledge');

echo "\n";
echo str_repeat('=', 60) . "\n";
echo "🌉 USTA → Yalıhan Bekçi Bridge\n";
echo str_repeat('=', 60) . "\n\n";

// 1. USTA pattern'lerini oku
if (!File::exists($ustaPatternsFile)) {
    echo "❌ USTA pattern dosyası bulunamadı: $ustaPatternsFile\n";
    echo "💡 Önce USTA test çalıştırın: node scripts/archive/usta-test.mjs\n";
    exit(1);
}

$ustaPatterns = json_decode(File::get($ustaPatternsFile), true);

if (!$ustaPatterns) {
    echo "❌ USTA pattern dosyası okunamadı veya boş\n";
    exit(1);
}

echo "📚 USTA Pattern'leri yüklendi:\n";
echo "   • Toplam pattern: {$ustaPatterns['totalPatterns']}\n";
echo "   • Son güncelleme: {$ustaPatterns['lastUpdated']}\n";
echo "   • Versiyon: {$ustaPatterns['version']}\n\n";

// 2. Yalıhan Bekçi'ye öğret
echo "🤖 Yalıhan Bekçi'ye öğretiliyor...\n\n";

$learnedCount = 0;
$errorCount = 0;

// Common Errors → Bekçi'ye öğret
if (isset($ustaPatterns['commonErrors']) && is_array($ustaPatterns['commonErrors'])) {
    foreach ($ustaPatterns['commonErrors'] as $error) {
        $context = "USTA Pattern: {$error['pattern']}";
        $details = [
            'pattern' => $error['pattern'],
            'context' => $error['context'],
            'solution' => $error['solution'],
            'autoFix' => $error['autoFix'] ?? null,
            'priority' => $error['priority'] ?? 'Medium',
            'frequency' => $error['frequency'] ?? 0,
            'examples' => $error['examples'] ?? [],
        ];

        try {
            Artisan::call('bekci:learn', [
                'action_type' => 'usta_pattern_learned',
                'context' => $context,
                '--details' => json_encode($details),
            ]);

            echo "   ✅ {$error['pattern']} öğretildi\n";
            $learnedCount++;
        } catch (\Exception $e) {
            echo "   ❌ {$error['pattern']} öğretilemedi: {$e->getMessage()}\n";
            $errorCount++;
        }
    }
}

// Best Practices → Bekçi'ye öğret
if (isset($ustaPatterns['bestPractices']) && is_array($ustaPatterns['bestPractices'])) {
    foreach ($ustaPatterns['bestPractices'] as $practice) {
        try {
            Artisan::call('bekci:learn', [
                'action_type' => 'usta_best_practice',
                'context' => "USTA Best Practice: $practice",
                '--details' => json_encode(['practice' => $practice]),
            ]);

            echo "   ✅ Best Practice öğretildi: $practice\n";
            $learnedCount++;
        } catch (\Exception $e) {
            echo "   ❌ Best Practice öğretilemedi: {$e->getMessage()}\n";
            $errorCount++;
        }
    }
}

// 3. Özet rapor oluştur
$reportFile = $bekciKnowledgeBase . '/usta-bridge-report-' . date('Y-m-d_H-i-s') . '.json';
$report = [
    'timestamp' => now()->toISOString(),
    'usta_version' => $ustaPatterns['version'],
    'usta_last_updated' => $ustaPatterns['lastUpdated'],
    'total_patterns' => $ustaPatterns['totalPatterns'],
    'learned_count' => $learnedCount,
    'error_count' => $errorCount,
    'success_rate' => $learnedCount > 0 ? round(($learnedCount / ($learnedCount + $errorCount)) * 100, 2) : 0,
    'patterns' => $ustaPatterns['commonErrors'] ?? [],
    'best_practices' => $ustaPatterns['bestPractices'] ?? [],
];

File::ensureDirectoryExists($bekciKnowledgeBase);
File::put($reportFile, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\n";
echo str_repeat('=', 60) . "\n";
echo "📊 Özet:\n";
echo str_repeat('=', 60) . "\n";
echo "   ✅ Öğretilen: $learnedCount\n";
echo "   ❌ Hata: $errorCount\n";
echo "   📈 Başarı Oranı: {$report['success_rate']}%\n";
echo "   📄 Rapor: $reportFile\n";
echo "\n";
echo "✨ Bridge tamamlandı!\n\n";
