<?php

$baseUrl = 'http://127.0.0.1:8000';
$adminPages = [
    '/admin',
    '/admin/dashboard',
    '/admin/ilanlar',
    '/admin/danisman',
    '/admin/kisiler',
    '/admin/kullanicilar',
    '/admin/ayarlar',
    '/admin/raporlar',
];

echo "\n🔍 ADMIN SAYFA TESTİ\n";
echo "═══════════════════════════════════════\n";
echo "Sunucu: {$baseUrl}\n";
echo "Test Zamanı: " . date('Y-m-d H:i:s') . "\n\n";

$results = [];
$totalTests = count($adminPages);
$passedTests = 0;
$errors = [];

foreach ($adminPages as $page) {
    $url = $baseUrl . $page;
    echo "Testing: {$page}\n";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_COOKIE, 'laravel_session=test_session');

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($response, $headerSize);

    curl_close($ch);

    $statusIcon = '❓';
    $errorMessage = '';

    if ($httpCode === 200) {
        $statusIcon = '✅';
        $passedTests++;
    } elseif ($httpCode === 302) {
        $statusIcon = '🔄';
        $errorMessage = 'Redirect (Login gerekli)';
    } elseif ($httpCode === 404) {
        $statusIcon = '❌';
        $errorMessage = 'Sayfa bulunamadı';
    } elseif ($httpCode === 500) {
        $statusIcon = '💥';
        preg_match('/<title>(.*?)<\/title>/s', $body, $matches);
        $errorTitle = $matches[1] ?? 'Internal Server Error';

        if (preg_match('/SQLSTATE\[42S02\].*Table.*doesn\'t exist/s', $body, $tableMatch)) {
            preg_match('/Table \'.*?\.(\w+)\'/s', $body, $tableName);
            $errorMessage = 'Tablo eksik: ' . ($tableName[1] ?? 'bilinmiyor');
        } elseif (preg_match('/Undefined variable \$(\w+)/s', $body, $varMatch)) {
            $errorMessage = 'Tanımsız değişken: $' . $varMatch[1];
        } elseif (preg_match('/Class ".*?\\\\(\w+)" not found/s', $body, $classMatch)) {
            $errorMessage = 'Sınıf bulunamadı: ' . $classMatch[1];
        } else {
            $errorMessage = strip_tags($errorTitle);
        }

        $errors[] = [
            'page' => $page,
            'error' => $errorMessage,
            'code' => $httpCode
        ];
    }

    echo "  {$statusIcon} HTTP {$httpCode}";
    if ($errorMessage) {
        echo " - {$errorMessage}";
    }
    echo "\n\n";
}

echo "\n📊 ÖZET İSTATİSTİKLER\n";
echo "═══════════════════════════════════════\n";
echo "Toplam Test: {$totalTests}\n";
echo "Başarılı: {$passedTests}\n";
echo "Redirect: " . ($totalTests - $passedTests - count($errors)) . "\n";
echo "Hatalı: " . count($errors) . "\n";

if (!empty($errors)) {
    echo "\n❌ BULUNAN HATALAR\n";
    echo "═══════════════════════════════════════\n";
    foreach ($errors as $error) {
        echo "📍 {$error['page']}\n";
        echo "   ⚠️  {$error['error']}\n\n";
    }
}

echo "\n✨ Test tamamlandı!\n";
