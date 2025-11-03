<?php

$baseUrl = 'http://127.0.0.1:8000';
$crudPages = [
    'İlanlar' => [
        'list' => '/admin/ilanlar',
        'create' => '/admin/ilanlar/create',
    ],
    'Danışmanlar' => [
        'list' => '/admin/danisman',
        'create' => '/admin/danisman/create',
    ],
    'Kişiler' => [
        'list' => '/admin/kisiler',
        'create' => '/admin/kisiler/create',
    ],
    'Kullanıcılar' => [
        'list' => '/admin/kullanicilar',
        'create' => '/admin/kullanicilar/create',
    ],
];

echo "\n🔍 ADMIN CRUD SAYFA TESTİ\n";
echo "═══════════════════════════════════════════════\n";
echo "Sunucu: {$baseUrl}\n";
echo "Test Zamanı: " . date('Y-m-d H:i:s') . "\n\n";

$totalTests = 0;
$passedTests = 0;
$errors = [];

foreach ($crudPages as $module => $pages) {
    echo "📦 {$module}\n";
    echo str_repeat('-', 47) . "\n";

    foreach ($pages as $type => $path) {
        $totalTests++;
        $url = $baseUrl . $path;
        $typeName = $type === 'list' ? 'Liste' : 'Oluştur';

        echo "  Testing: {$typeName} ({$path})\n";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

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
            $errorMessage = 'Redirect';
        } elseif ($httpCode === 404) {
            $statusIcon = '❌';
            $errorMessage = 'Route bulunamadı';
            $errors[] = [
                'module' => $module,
                'type' => $typeName,
                'path' => $path,
                'error' => $errorMessage
            ];
        } elseif ($httpCode === 500) {
            $statusIcon = '💥';

            if (preg_match('/SQLSTATE\[42S02\].*Table \'.*?\.(\w+)\'/s', $body, $tableName)) {
                $errorMessage = 'Tablo eksik: ' . $tableName[1];
            } elseif (preg_match('/Undefined variable \$(\w+)/s', $body, $varMatch)) {
                $errorMessage = 'Tanımsız değişken: $' . $varMatch[1];
            } elseif (preg_match('/Class ".*?\\\\(\w+)" not found/s', $body, $classMatch)) {
                $errorMessage = 'Model eksik: ' . $classMatch[1];
            } elseif (preg_match('/Method.*?does not exist/s', $body)) {
                $errorMessage = 'Method bulunamadı';
            } else {
                preg_match('/<title>(.*?)<\/title>/s', $body, $matches);
                $errorMessage = strip_tags($matches[1] ?? 'Internal Server Error');
            }

            $errors[] = [
                'module' => $module,
                'type' => $typeName,
                'path' => $path,
                'error' => $errorMessage
            ];
        }

        echo "    {$statusIcon} HTTP {$httpCode}";
        if ($errorMessage) {
            echo " - {$errorMessage}";
        }
        echo "\n";
    }
    echo "\n";
}

echo "\n📊 ÖZET İSTATİSTİKLER\n";
echo "═══════════════════════════════════════════════\n";
echo "Toplam Test: {$totalTests}\n";
echo "Başarılı: {$passedTests}\n";
echo "Hatalı: " . count($errors) . "\n";
echo "Başarı Oranı: " . round(($passedTests / $totalTests) * 100, 2) . "%\n";

if (!empty($errors)) {
    echo "\n❌ DÜZELTILMESI GEREKEN HATALAR\n";
    echo "═══════════════════════════════════════════════\n";
    foreach ($errors as $error) {
        echo "📍 {$error['module']} - {$error['type']}\n";
        echo "   Path: {$error['path']}\n";
        echo "   ⚠️  {$error['error']}\n\n";
    }
}

echo "✨ Test tamamlandı!\n";
