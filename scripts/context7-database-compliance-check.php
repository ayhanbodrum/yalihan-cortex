#!/usr/bin/env php
<?php

/**
 * Context7 Database Schema Compliance Checker
 *
 * Bu script, veritabanındaki tüm tabloları tarar ve Context7 kurallarına uygunluğunu kontrol eder.
 * Özellikle 'status' kolonu eksikliği ve yanlış kolon adları tespit edilir.
 *
 * Kullanım: php scripts/context7-database-compliance-check.php
 *
 * Context7 Standard: C7-DB-COMPLIANCE-CHECK-2025-11-09
 */

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Context7 Database Schema Compliance Checker\n";
echo "==============================================\n\n";

$issues = [];
$tables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()");

foreach ($tables as $table) {
    $tableName = $table->TABLE_NAME;

    // Skip system tables
    if (in_array($tableName, ['migrations', 'telescope_entries', 'telescope_entries_tags', 'telescope_monitoring'])) {
        continue;
    }

    $columns = DB::select("SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?", [$tableName]);
    $columnNames = array_map(fn($col) => $col->COLUMN_NAME, $columns);

    // Check 1: status kolonu var mı?
    $hasStatus = in_array('status', $columnNames);
    $hasEnabled = in_array('enabled', $columnNames);
    $hasIsActive = in_array('is_active', $columnNames);
    $hasAktif = in_array('aktif', $columnNames);
    $hasDurum = in_array('durum', $columnNames);

    // Check 2: order kolonu var mı? (Context7: order → display_order)
    $hasOrder = in_array('order', $columnNames);
    $hasDisplayOrder = in_array('display_order', $columnNames);

    // Check 2: Kodda status kullanılıyor mu?
    $codeUsesStatus = false;
    $modelFile = app_path("Models/{$tableName}.php");
    $moduleModelFile = glob(app_path("Modules/*/Models/{$tableName}.php"));

    if (file_exists($modelFile) || !empty($moduleModelFile)) {
        $modelContent = file_exists($modelFile) ? file_get_contents($modelFile) : file_get_contents($moduleModelFile[0]);
        $codeUsesStatus = strpos($modelContent, "->where('status'") !== false ||
                         strpos($modelContent, "->where(\"status\"") !== false ||
                         strpos($modelContent, "'status'") !== false ||
                         strpos($modelContent, '"status"') !== false;
    }

    // Issue detection
    if ($codeUsesStatus && !$hasStatus) {
        $issues[] = [
            'table' => $tableName,
            'severity' => 'CRITICAL',
            'issue' => "Kod 'status' kolonunu kullanıyor ama tabloda 'status' kolonu yok",
            'has_enabled' => $hasEnabled,
            'has_is_active' => $hasIsActive,
            'has_aktif' => $hasAktif,
            'has_durum' => $hasDurum,
        ];
    }

    if ($hasEnabled || $hasIsActive || $hasAktif) {
        $issues[] = [
            'table' => $tableName,
            'severity' => 'WARNING',
            'issue' => "Context7 ihlali: 'enabled', 'is_active' veya 'aktif' kolonu kullanılıyor. 'status' kullanılmalı",
            'has_enabled' => $hasEnabled,
            'has_is_active' => $hasIsActive,
            'has_aktif' => $hasAktif,
        ];
    }

    // Check 3: order kolonu var mı? (Context7: order → display_order)
    if ($hasOrder && !$hasDisplayOrder) {
        $issues[] = [
            'table' => $tableName,
            'severity' => 'CRITICAL',
            'issue' => "Context7 ihlali: 'order' kolonu kullanılıyor. 'display_order' kullanılmalı",
            'has_order' => $hasOrder,
            'has_display_order' => $hasDisplayOrder,
        ];
    }
}

// Report
if (empty($issues)) {
    echo "✅ Tüm tablolar Context7 kurallarına uygun!\n";
    exit(0);
}

echo "❌ " . count($issues) . " sorun tespit edildi:\n\n";

$criticalCount = 0;
$warningCount = 0;

foreach ($issues as $issue) {
    if ($issue['severity'] === 'CRITICAL') {
        $criticalCount++;
        echo "🔴 [CRITICAL] {$issue['table']}: {$issue['issue']}\n";
        if ($issue['has_durum']) {
            echo "   → 'durum' kolonu var, 'status' kolonuna migration yapılmalı\n";
        }
        if ($issue['has_enabled']) {
            echo "   → 'enabled' kolonu var, 'status' kolonuna migration yapılmalı\n";
        }
        if (isset($issue['has_order']) && $issue['has_order']) {
            echo "   → 'order' kolonu var, 'display_order' kolonuna migration yapılmalı\n";
            echo "   → Migration: php artisan make:migration rename_order_to_display_order_in_{$issue['table']}_table\n";
        }
    } else {
        $warningCount++;
        echo "⚠️  [WARNING] {$issue['table']}: {$issue['issue']}\n";
    }
    echo "\n";
}

echo "\n📊 Özet:\n";
echo "   - Critical: {$criticalCount}\n";
echo "   - Warning: {$warningCount}\n";
echo "   - Toplam: " . count($issues) . "\n\n";

if ($criticalCount > 0) {
    echo "🚨 CRITICAL sorunlar var! Migration yapılmalı.\n";
    exit(1);
}

exit(0);

