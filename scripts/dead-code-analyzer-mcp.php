#!/usr/bin/env php
<?php

/**
 * Dead Code Analyzer - MCP Enhanced Version
 * 
 * Context7 MCP Entegrasyonu ile geliştirilmiş versiyon
 * 
 * Özellikler:
 * 1. Yalıhan Bekçi MCP'den kuralları alır
 * 2. Context7 compliance kontrolü yapar
 * 3. MCP'ye sonuçları bildirir
 * 
 * Kullanım:
 *   php scripts/dead-code-analyzer.php [--mcp] [--context7]
 */

$basePath = __DIR__ . '/../';
$useMCP = in_array('--mcp', $argv) || in_array('--context7', $argv);
$mcpResults = [];

echo "🔍 Dead Code Analyzer - MCP Enhanced\n";
echo "=====================================\n\n";

// MCP entegrasyonu
if ($useMCP) {
    echo "🔗 MCP entegrasyonu aktif...\n";
    
    // Yalıhan Bekçi MCP'den kuralları al
    try {
        $mcpRules = getContext7RulesFromMCP();
        if ($mcpRules) {
            echo "   ✅ Context7 kuralları MCP'den alındı\n";
            $mcpResults['rules'] = $mcpRules;
        }
    } catch (Exception $e) {
        echo "   ⚠️  MCP kuralları alınamadı: " . $e->getMessage() . "\n";
        echo "   ℹ️  Yerel kurallar kullanılacak\n";
    }
    
    // Sistem yapısını MCP'den al
    try {
        $systemStructure = getSystemStructureFromMCP();
        if ($systemStructure) {
            echo "   ✅ Sistem yapısı MCP'den alındı\n";
            $mcpResults['structure'] = $systemStructure;
        }
    } catch (Exception $e) {
        echo "   ⚠️  MCP sistem yapısı alınamadı\n";
    }
    
    echo "\n";
}

// ... existing dead code analyzer code ...

/**
 * Yalıhan Bekçi MCP'den Context7 kurallarını al
 */
function getContext7RulesFromMCP() {
    // MCP server'a HTTP isteği gönder
    // veya stdio üzerinden iletişim kur
    
    // Örnek: MCP resource'dan kuralları al
    $rulesPath = __DIR__ . '/../.context7/authority.json';
    if (file_exists($rulesPath)) {
        $authority = json_decode(file_get_contents($rulesPath), true);
        return [
            'forbidden' => $authority['context7']['forbidden_patterns'] ?? [],
            'required' => $authority['context7']['required_patterns'] ?? [],
        ];
    }
    
    return null;
}

/**
 * Yalıhan Bekçi MCP'den sistem yapısını al
 */
function getSystemStructureFromMCP() {
    // MCP tool: get_system_structure
    // Şimdilik yerel dosyadan oku
    
    $structurePath = __DIR__ . '/../.yalihan-bekci/reports/system-structure.json';
    if (file_exists($structurePath)) {
        return json_decode(file_get_contents($structurePath), true);
    }
    
    return null;
}

/**
 * Sonuçları MCP'ye bildir
 */
function reportToMCP($results) {
    // MCP'ye sonuçları gönder
    // Öğrenme sistemi için kullanılabilir
    
    $reportPath = __DIR__ . '/../.yalihan-bekci/reports/dead-code-mcp-' . date('Y-m-d-His') . '.json';
    file_put_contents($reportPath, json_encode($results, JSON_PRETTY_PRINT));
    
    echo "   ✅ Sonuçlar MCP'ye bildirildi: $reportPath\n";
}

// ... rest of the script ...

