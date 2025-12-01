<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Context7 Compliance: musteri_segmenti → kisi_segmenti
 *
 * Bu migration kisiler tablosundaki musteri_segmenti kolonunu kisi_segmenti olarak yeniden adlandırır.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = 'kisiler';

        if (!Schema::hasTable($tableName)) {
            echo "⚠️ Table {$tableName} does not exist. Skipping...\n";
            return;
        }

        $hasOldColumn = Schema::hasColumn($tableName, 'musteri_segmenti');
        $hasNewColumn = Schema::hasColumn($tableName, 'kisi_segmenti');

        if ($hasOldColumn && !$hasNewColumn) {
            // 1. Index'leri kontrol et ve kaldır
            $this->dropIndexesForColumn($tableName, 'musteri_segmenti');

            // 2. Kolon bilgilerini al
            $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'musteri_segmenti'");
            if (!empty($columnInfo)) {
                $col = $columnInfo[0];
                $columnType = $col->Type;
                $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $col->Default !== null
                    ? "DEFAULT '{$col->Default}'"
                    : ($col->Null === 'YES' ? 'DEFAULT NULL' : '');

                // 3. MySQL'de direkt SQL ile rename
                DB::statement("ALTER TABLE `{$tableName}` CHANGE `musteri_segmenti` `kisi_segmenti` {$columnType} {$isNullable} {$default}");
            } else {
                // Fallback: Varsayılan tip
                DB::statement("ALTER TABLE `{$tableName}` CHANGE `musteri_segmenti` `kisi_segmenti` VARCHAR(255) NULL");
            }

            // 4. Index'leri yeniden oluştur
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                try {
                    $table->index('kisi_segmenti', "idx_{$tableName}_kisi_segmenti");
                } catch (\Exception $e) {
                    // Index zaten varsa devam et
                }
            });

            echo "✅ Renamed: {$tableName}.musteri_segmenti → {$tableName}.kisi_segmenti\n";
        } elseif ($hasOldColumn && $hasNewColumn) {
            // Her iki kolon da varsa, veriyi migrate et
            DB::statement("UPDATE `{$tableName}` SET kisi_segmenti = COALESCE(kisi_segmenti, musteri_segmenti) WHERE kisi_segmenti IS NULL");
            echo "⚠️ Both columns exist: {$tableName}. Migrated data from musteri_segmenti to kisi_segmenti\n";
        } else {
            echo "✅ Column kisi_segmenti already exists in {$tableName}. Skipping...\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = 'kisiler';

        if (!Schema::hasTable($tableName)) {
            return;
        }

        $hasNewColumn = Schema::hasColumn($tableName, 'kisi_segmenti');

        if ($hasNewColumn) {
            // Rollback: kisi_segmenti → musteri_segmenti
            $this->dropIndexesForColumn($tableName, 'kisi_segmenti');

            $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'kisi_segmenti'");
            if (!empty($columnInfo)) {
                $col = $columnInfo[0];
                $columnType = $col->Type;
                $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $col->Default !== null
                    ? "DEFAULT '{$col->Default}'"
                    : ($col->Null === 'YES' ? 'DEFAULT NULL' : '');

                DB::statement("ALTER TABLE `{$tableName}` CHANGE `kisi_segmenti` `musteri_segmenti` {$columnType} {$isNullable} {$default}");
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                try {
                    $table->index('musteri_segmenti', "idx_{$tableName}_musteri_segmenti");
                } catch (\Exception $e) {
                    // Index zaten varsa devam et
                }
            });

            echo "✅ Rolled back: {$tableName}.kisi_segmenti → {$tableName}.musteri_segmenti\n";
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
