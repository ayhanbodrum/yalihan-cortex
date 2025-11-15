<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Context7 Compliance: is_active → status
 *
 * Bu migration aşağıdaki tablodaki `is_active` kolonunu `status` olarak yeniden adlandırır:
 * - ai_core_system.is_active → status
 *
 * Context7 Standard: MIGRATION_STANDARDS.md
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = 'ai_core_system';

        if (!Schema::hasTable($tableName)) {
            return;
        }

        $hasIsActive = Schema::hasColumn($tableName, 'is_active');
        $hasStatus = Schema::hasColumn($tableName, 'status');

        if ($hasIsActive && !$hasStatus) {
            // 1. Index'leri kontrol et ve kaldır
            $this->dropIndexesForColumn($tableName, 'is_active');

            // 2. Kolon bilgilerini al
            $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'is_active'");
            if (!empty($columnInfo)) {
                $col = $columnInfo[0];
                $columnType = $col->Type;
                $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $col->Default !== null
                    ? "DEFAULT '{$col->Default}'"
                    : ($col->Null === 'YES' ? 'DEFAULT NULL' : 'DEFAULT 0');

                // 3. MySQL'de direkt SQL ile rename (Context7: DB::statement kullan)
                DB::statement("ALTER TABLE `{$tableName}` CHANGE `is_active` `status` {$columnType} {$isNullable} {$default}");
            } else {
                // Fallback: Varsayılan tip
                DB::statement("ALTER TABLE `{$tableName}` CHANGE `is_active` `status` TINYINT(1) NOT NULL DEFAULT 1");
            }

            // 4. Index'leri yeniden oluştur
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                try {
                    $table->index('status', "idx_{$tableName}_status");
                } catch (\Exception $e) {
                    // Index zaten varsa devam et
                }
            });

            echo "✅ Renamed: {$tableName}.is_active → {$tableName}.status\n";
        } elseif ($hasIsActive && $hasStatus) {
            // Her iki kolon da varsa, veriyi migrate et
            DB::statement("UPDATE `{$tableName}` SET status = COALESCE(status, is_active) WHERE status IS NULL");
            echo "⚠️ Both columns exist: {$tableName}. Migrated data from is_active to status\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = 'ai_core_system';

        if (!Schema::hasTable($tableName)) {
            return;
        }

        $hasStatus = Schema::hasColumn($tableName, 'status');

        if ($hasStatus) {
            // Rollback: status → is_active
            $this->dropIndexesForColumn($tableName, 'status');

            $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'status'");
            if (!empty($columnInfo)) {
                $col = $columnInfo[0];
                $columnType = $col->Type;
                $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $col->Default !== null
                    ? "DEFAULT '{$col->Default}'"
                    : ($col->Null === 'YES' ? 'DEFAULT NULL' : 'DEFAULT 0');

                DB::statement("ALTER TABLE `{$tableName}` CHANGE `status` `is_active` {$columnType} {$isNullable} {$default}");
            } else {
                DB::statement("ALTER TABLE `{$tableName}` CHANGE `status` `is_active` TINYINT(1) NOT NULL DEFAULT 1");
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                try {
                    $table->index('is_active', "idx_{$tableName}_is_active");
                } catch (\Exception $e) {
                    // Index zaten varsa devam et
                }
            });
        }
    }

    /**
     * Helper: Kolon için index'leri kaldır
     */
    private function dropIndexesForColumn(string $tableName, string $columnName): void
    {
        try {
            $indexes = DB::select("SHOW INDEXES FROM `{$tableName}` WHERE Column_name = ?", [$columnName]);
            foreach ($indexes as $index) {
                if ($index->Key_name !== 'PRIMARY') {
                    Schema::table($tableName, function (Blueprint $table) use ($index) {
                        try {
                            $table->dropIndex($index->Key_name);
                        } catch (\Exception $e) {
                            // Index zaten yoksa devam et
                        }
                    });
                }
            }
        } catch (\Exception $e) {
            // Hata durumunda devam et
        }
    }
};
