<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Context7 Compliance: aktif → status
 *
 * Bu migration aşağıdaki tablolardaki `aktif` kolonlarını `status` olarak yeniden adlandırır:
 * - kategori_ozellik_matrix.aktif → status
 * - konut_ozellik_hibrit_siralama.aktif → status
 * - ozellik_alt_kategorileri.aktif → status
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
        $tables = [
            'kategori_ozellik_matrix',
            'konut_ozellik_hibrit_siralama',
            'ozellik_alt_kategorileri',
        ];

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            $hasAktif = Schema::hasColumn($tableName, 'aktif');
            $hasStatus = Schema::hasColumn($tableName, 'status');

            if ($hasAktif && ! $hasStatus) {
                // 1. Index'leri kontrol et ve kaldır
                $this->dropIndexesForColumn($tableName, 'aktif');

                // 2. Kolon bilgilerini al
                $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'aktif'");
                if (! empty($columnInfo)) {
                    $col = $columnInfo[0];
                    $columnType = $col->Type;
                    $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                    $default = $col->Default !== null
                        ? "DEFAULT '{$col->Default}'"
                        : ($col->Null === 'YES' ? 'DEFAULT NULL' : 'DEFAULT 0');

                    // 3. MySQL'de direkt SQL ile rename (Context7: DB::statement kullan)
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `aktif` `status` {$columnType} {$isNullable} {$default}");
                } else {
                    // Fallback: Varsayılan tip
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `aktif` `status` TINYINT(1) NOT NULL DEFAULT 1");
                }

                // 4. Index'leri yeniden oluştur
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    try {
                        $table->index('status', "idx_{$tableName}_status");
                    } catch (\Exception $e) {
                        // Index zaten varsa devam et
                    }
                });

                echo "✅ Renamed: {$tableName}.aktif → {$tableName}.status\n";
            } elseif ($hasAktif && $hasStatus) {
                // Her iki kolon da varsa, veriyi migrate et
                DB::statement("UPDATE `{$tableName}` SET status = COALESCE(status, aktif) WHERE status IS NULL");
                echo "⚠️ Both columns exist: {$tableName}. Migrated data from aktif to status\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'ozellik_alt_kategorileri',
            'konut_ozellik_hibrit_siralama',
            'kategori_ozellik_matrix',
        ];

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            $hasStatus = Schema::hasColumn($tableName, 'status');

            if ($hasStatus) {
                // Rollback: status → aktif
                $this->dropIndexesForColumn($tableName, 'status');

                $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'status'");
                if (! empty($columnInfo)) {
                    $col = $columnInfo[0];
                    $columnType = $col->Type;
                    $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                    $default = $col->Default !== null
                        ? "DEFAULT '{$col->Default}'"
                        : ($col->Null === 'YES' ? 'DEFAULT NULL' : 'DEFAULT 0');

                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `status` `aktif` {$columnType} {$isNullable} {$default}");
                } else {
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `status` `aktif` TINYINT(1) NOT NULL DEFAULT 1");
                }

                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    try {
                        $table->index('aktif', "idx_{$tableName}_aktif");
                    } catch (\Exception $e) {
                        // Index zaten varsa devam et
                    }
                });
            }
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
