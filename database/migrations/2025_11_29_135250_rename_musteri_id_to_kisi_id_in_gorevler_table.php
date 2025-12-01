<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Context7 Compliance: musteri_id → kisi_id
 *
 * Bu migration gorevler tablosundaki musteri_id kolonunu kisi_id olarak yeniden adlandırır.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = 'gorevler';

        if (!Schema::hasTable($tableName)) {
            echo "⚠️ Table {$tableName} does not exist. Skipping...\n";
            return;
        }

        $hasOldColumn = Schema::hasColumn($tableName, 'musteri_id');
        $hasNewColumn = Schema::hasColumn($tableName, 'kisi_id');

        if ($hasOldColumn && !$hasNewColumn) {
            // 1. Index'leri kontrol et ve kaldır
            $this->dropIndexesForColumn($tableName, 'musteri_id');

            // 2. Kolon bilgilerini al
            $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'musteri_id'");
            if (!empty($columnInfo)) {
                $col = $columnInfo[0];
                $columnType = $col->Type;
                $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $col->Default !== null
                    ? "DEFAULT '{$col->Default}'"
                    : ($col->Null === 'YES' ? 'DEFAULT NULL' : '');

                // 3. MySQL'de direkt SQL ile rename
                DB::statement("ALTER TABLE `{$tableName}` CHANGE `musteri_id` `kisi_id` {$columnType} {$isNullable} {$default}");
            } else {
                // Fallback: Varsayılan tip
                DB::statement("ALTER TABLE `{$tableName}` CHANGE `musteri_id` `kisi_id` BIGINT UNSIGNED NULL");
            }

            // 4. Index'leri yeniden oluştur
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                try {
                    $table->index('kisi_id', "idx_{$tableName}_kisi_id");
                } catch (\Exception $e) {
                    // Index zaten varsa devam et
                }
            });

            echo "✅ Renamed: {$tableName}.musteri_id → {$tableName}.kisi_id\n";
        } elseif ($hasOldColumn && $hasNewColumn) {
            // Her iki kolon da varsa, veriyi migrate et
            DB::statement("UPDATE `{$tableName}` SET kisi_id = COALESCE(kisi_id, musteri_id) WHERE kisi_id IS NULL");
            echo "⚠️ Both columns exist: {$tableName}. Migrated data from musteri_id to kisi_id\n";
        } else {
            echo "✅ Column kisi_id already exists in {$tableName}. Skipping...\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = 'gorevler';

        if (!Schema::hasTable($tableName)) {
            return;
        }

        $hasNewColumn = Schema::hasColumn($tableName, 'kisi_id');

        if ($hasNewColumn) {
            // Rollback: kisi_id → musteri_id
            $this->dropIndexesForColumn($tableName, 'kisi_id');

            $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'kisi_id'");
            if (!empty($columnInfo)) {
                $col = $columnInfo[0];
                $columnType = $col->Type;
                $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $col->Default !== null
                    ? "DEFAULT '{$col->Default}'"
                    : ($col->Null === 'YES' ? 'DEFAULT NULL' : '');

                DB::statement("ALTER TABLE `{$tableName}` CHANGE `kisi_id` `musteri_id` {$columnType} {$isNullable} {$default}");
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                try {
                    $table->index('musteri_id', "idx_{$tableName}_musteri_id");
                } catch (\Exception $e) {
                    // Index zaten varsa devam et
                }
            });

            echo "✅ Rolled back: {$tableName}.kisi_id → {$tableName}.musteri_id\n";
        }
    }

    /**
     * Helper: Kolon için index'leri kaldır
     */
    private function dropIndexesForColumn(string $tableName, string $columnName): void
    {
        $indexes = DB::select("SHOW INDEXES FROM `{$tableName}` WHERE Column_name = '{$columnName}'");

        foreach ($indexes as $index) {
            if ($index->Key_name !== 'PRIMARY') {
                try {
                    DB::statement("ALTER TABLE `{$tableName}` DROP INDEX `{$index->Key_name}`");
                } catch (\Exception $e) {
                    // Index zaten yoksa devam et
                }
            }
        }
    }
};
