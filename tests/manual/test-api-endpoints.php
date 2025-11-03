<?php

$baseUrl = 'http://127.0.0.1:8000';
$endpoints = [
    ['method' => 'GET', 'url' => '/api/health', 'name' => 'Sağlık Kontrolü'],
    ['method' => 'GET', 'url' => '/api/ai/status', 'name' => 'AI Durum'],
    ['method' => 'GET', 'url' => '/api/location/iller', 'name' => 'İller Listesi'],
    ['method' => 'GET', 'url' => '/api/hybrid-search/kisiler', 'name' => 'Kişiler Hibrid Arama'],
];

echo "\n🧪 API ENDPOINT TEST RAPORU\n";
echo "============================\n";
echo "Sunucu: {$baseUrl}\n";
echo "Test Zamanı: " . date('Y-m-d H:i:s') . "\n\n";

$results = [];
$totalTests = count($endpoints);
$passedTests = 0;

foreach ($endpoints as $endpoint) {
    $url = $baseUrl . $endpoint['url'];
    $name = $endpoint['name'];

    echo "Testing: {$name}\n";
    echo "  URL: {$url}\n";

    $startTime = microtime(true);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $responseTime = (microtime(true) - $startTime) * 1000;

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    $error = curl_error($ch);
    curl_close($ch);

    $passed = ($httpCode >= 200 && $httpCode < 300);
    if ($passed) $passedTests++;

    $result = [
        'name' => $name,
        'url' => $url,
        'status_code' => $httpCode,
        'response_time' => round($responseTime, 2),
        'passed' => $passed,
        'error' => $error,
        'body' => $body
    ];

    $results[] = $result;

    $statusIcon = $passed ? '✅' : '❌';
    echo "  {$statusIcon} HTTP {$httpCode} - {$responseTime}ms\n";

    if (!$passed && $error) {
        echo "  ⚠️  Hata: {$error}\n";
    }

    if ($passed && $body) {
        $json = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "  📦 JSON yanıt geçerli\n";
            if (isset($json['status'])) {
                echo "  📊 Durum: {$json['status']}\n";
            }
        } else {
            echo "  ⚠️  JSON parse hatası\n";
        }
    }

    echo "\n";
}

echo "\n📊 ÖZET İSTATİSTİKLER\n";
echo "====================\n";
echo "Toplam Test: {$totalTests}\n";
echo "Başarılı: {$passedTests}\n";
echo "Başarısız: " . ($totalTests - $passedTests) . "\n";
echo "Başarı Oranı: " . round(($passedTests / $totalTests) * 100, 2) . "%\n\n";

echo "✅ Context7 Uyumluluk Kontrolü\n";
echo "================================\n";
exec('php artisan context7:check 2>&1', $output, $returnCode);
echo implode("\n", $output) . "\n\n";

echo "📁 Son Log Kayıtları (storage/logs)\n";
echo "====================================\n";
$logFile = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logs = file($logFile);
    $recentLogs = array_slice($logs, -20);
    foreach ($recentLogs as $log) {
        if (strpos($log, 'ERROR') !== false || strpos($log, 'WARNING') !== false) {
            echo $log;
        }
    }
} else {
    echo "Log dosyası bulunamadı\n";
}

echo "\n✨ Test tamamlandı!\n";
