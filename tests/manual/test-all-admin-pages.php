<?php

$baseUrl = 'http://127.0.0.1:8000';
$pages = [
    '/admin/takim-yonetimi/gorevler',
    '/admin/ai-settings',
    '/admin/ozellikler/kategoriler',
    '/admin/takim-yonetimi/takim',
    '/admin/takim-yonetimi/takim/performans',
    '/admin/telegram-bot',
    '/admin/adres-yonetimi',
];

echo "\n🔍 TÜM ADMIN SAYFALAR TESTİ\n";
echo "═══════════════════════════════════════\n\n";

$total = count($pages);
$passed = 0;

foreach ($pages as $page) {
    $ch = curl_init($baseUrl . $page);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $icon = ($httpCode === 200 || $httpCode === 302) ? '✅' : '❌';
    if ($httpCode === 200 || $httpCode === 302) $passed++;

    echo "{$icon} {$page}\n";
    echo "   HTTP {$httpCode}\n\n";
}

echo "═══════════════════════════════════════\n";
echo "Toplam: {$total}\n";
echo "Başarılı: {$passed}\n";
echo "Başarı Oranı: " . round(($passed / $total) * 100, 2) . "%\n";
echo "\n✨ Test tamamlandı!\n";
