#!/usr/bin/env php
<?php

/**
 * Context7 MCP Test Script
 *
 * Bu script Context7 MCP'nin çalışıp çalışmadığını test eder.
 *
 * Kullanım:
 *   php scripts/test-context7-mcp.php
 */
echo "🔍 Context7 MCP Test Script\n";
echo "==========================\n\n";

// 1. MCP.json kontrolü
echo "1️⃣  MCP.json kontrolü...\n";
$mcpJsonPath = $_SERVER['HOME'].'/.cursor/mcp.json';

if (! file_exists($mcpJsonPath)) {
    echo "   ❌ mcp.json dosyası bulunamadı: {$mcpJsonPath}\n";
    exit(1);
}

$mcpConfig = json_decode(file_get_contents($mcpJsonPath), true);

if (! isset($mcpConfig['mcpServers']['context7'])) {
    echo "   ❌ Context7 MCP konfigürasyonu bulunamadı\n";
    exit(1);
}

$context7Config = $mcpConfig['mcpServers']['context7'];
echo "   ✅ Context7 MCP konfigürasyonu bulundu\n";
echo "      Command: {$context7Config['command']}\n";
echo '      Args: '.implode(' ', $context7Config['args'])."\n";

// 2. API Key kontrolü
echo "\n2️⃣  API Key kontrolü...\n";
$apiKey = null;
foreach ($context7Config['args'] as $arg) {
    if (strpos($arg, 'ctx7sk-') === 0) {
        $apiKey = $arg;
        break;
    }
}

if (! $apiKey) {
    // Environment variable kontrolü
    if (isset($context7Config['env']['CONTEXT7_API_KEY'])) {
        $apiKey = $context7Config['env']['CONTEXT7_API_KEY'];
    }
}

if (! $apiKey) {
    echo "   ⚠️  API Key bulunamadı (opsiyonel - rate limit ile çalışabilir)\n";
} else {
    echo '   ✅ API Key bulundu: '.substr($apiKey, 0, 20)."...\n";
}

// 3. Node.js/npx kontrolü
echo "\n3️⃣  Node.js/npx kontrolü...\n";
$nodeVersion = shell_exec('node --version 2>&1');
if ($nodeVersion) {
    echo '   ✅ Node.js yüklü: '.trim($nodeVersion)."\n";
} else {
    echo "   ❌ Node.js yüklü değil\n";
    exit(1);
}

$npxVersion = shell_exec('npx --version 2>&1');
if ($npxVersion) {
    echo '   ✅ npx yüklü: '.trim($npxVersion)."\n";
} else {
    echo "   ❌ npx yüklü değil\n";
    exit(1);
}

// 4. Context7 MCP paketi kontrolü
echo "\n4️⃣  Context7 MCP paketi kontrolü...\n";
$packageCheck = shell_exec('npx -y @upstash/context7-mcp --help 2>&1');
if (strpos($packageCheck, 'context7') !== false || strpos($packageCheck, 'Usage') !== false) {
    echo "   ✅ Context7 MCP paketi erişilebilir\n";
} else {
    echo "   ⚠️  Context7 MCP paketi kontrol edilemedi (ilk kullanımda otomatik yüklenecek)\n";
}

// 5. Proje içi Context7 entegrasyonu kontrolü
echo "\n5️⃣  Proje içi Context7 entegrasyonu kontrolü...\n";
$projectRoot = dirname(__DIR__);

$checks = [
    '.cursorrules' => 'Cursor Rules dosyası',
    'config/context7.php' => 'Context7 config dosyası',
    '.context7/authority.json' => 'Context7 authority dosyası',
];

foreach ($checks as $file => $description) {
    $filePath = $projectRoot.'/'.$file;
    if (file_exists($filePath)) {
        echo "   ✅ {$description}: {$file}\n";
    } else {
        echo "   ⚠️  {$description} bulunamadı: {$file}\n";
    }
}

// 6. Özet
echo "\n".str_repeat('=', 50)."\n";
echo "📊 ÖZET\n";
echo str_repeat('=', 50)."\n";
echo "✅ Context7 MCP kurulumu tamamlandı\n";
echo "✅ MCP.json konfigürasyonu doğru\n";
echo "✅ Node.js/npx hazır\n";
echo "\n💡 Kullanım:\n";
echo "   - Cursor'da kod yazarken Context7 otomatik olarak kullanılacak\n";
echo "   - Laravel, React gibi kütüphaneler için güncel dokümantasyon sağlanacak\n";
echo "   - Kullanıcı açıkça istemeden Context7 MCP çalışacak\n";
echo "\n📚 Dokümantasyon:\n";
echo "   - docs/technical/context7-mcp-integration.md\n";
echo "   - .cursorrules (Context7 MCP kuralları)\n";
echo "\n";
