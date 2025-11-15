<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Context7 Compliance: Rename 'order' column to 'display_order'
     * 
     * This migration renames the 'order' column to 'display_order' in tables
     * that still use the old naming convention.
     */
    public function up(): void
    {
        // Tablolar ve kolonları
        $tablesToRename = [
            'ilan_kategorileri',
            'ilan_kategori_yayin_tipleri',
            'ozellik_kategorileri',
        ];

        foreach ($tablesToRename as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            // order kolonu var mı ve display_order yok mu kontrol et
            $hasOrder = Schema::hasColumn($tableName, 'order');
            $hasDisplayOrder = Schema::hasColumn($tableName, 'display_order');

            if ($hasOrder && !$hasDisplayOrder) {
                // Index'leri kontrol et ve kaldır
                $this->dropIndexesForColumn($tableName, 'order');

                // Kolon tipini al
                $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'order'");
                if (!empty($columnInfo)) {
                    $col = $columnInfo[0];
                    $columnType = $col->Type;
                    $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                    $default = $col->Default !== null ? "DEFAULT {$col->Default}" : ($col->Null === 'YES' ? 'DEFAULT NULL' : 'DEFAULT 0');
                    
                    // MySQL'de direkt SQL ile rename (doctrine/dbal gerektirmez)
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `order` `display_order` {$columnType} {$isNullable} {$default}");
                } else {
                    // Fallback: Varsayılan tip
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `order` `display_order` INT NOT NULL DEFAULT 0");
                }

                // Index'leri yeniden oluştur
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    try {
                        $table->index('display_order', "idx_{$tableName}_display_order");
                    } catch (\Exception $e) {
                        // Index zaten varsa devam et
                    }
                });

                echo "✅ Renamed: {$tableName}.order → {$tableName}.display_order\n";
            } elseif ($hasOrder && $hasDisplayOrder) {
                // Her ikisi de varsa, order'dan display_order'a veri taşı ve order'ı kaldır
                DB::statement("UPDATE `{$tableName}` SET display_order = COALESCE(display_order, `order`) WHERE display_order IS NULL OR display_order = 0");
                
                $this->dropIndexesForColumn($tableName, 'order');
                
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('order');
                });

                echo "✅ Merged and removed: {$tableName}.order → {$tableName}.display_order\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablesToRestore = [
            'ilan_kategorileri',
            'ilan_kategori_yayin_tipleri',
            'ozellik_kategorileri',
        ];

        foreach ($tablesToRestore as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            $hasDisplayOrder = Schema::hasColumn($tableName, 'display_order');
            $hasOrder = Schema::hasColumn($tableName, 'order');

            if ($hasDisplayOrder && !$hasOrder) {
                // Index'leri kontrol et ve kaldır
                $this->dropIndexesForColumn($tableName, 'display_order');

                // Kolon tipini al
                $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'display_order'");
                if (!empty($columnInfo)) {
                    $col = $columnInfo[0];
                    $columnType = $col->Type;
                    $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                    $default = $col->Default !== null ? "DEFAULT {$col->Default}" : ($col->Null === 'YES' ? 'DEFAULT NULL' : 'DEFAULT 0');
                    
                    // MySQL'de direkt SQL ile rename (rollback)
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `display_order` `order` {$columnType} {$isNullable} {$default}");
                } else {
                    // Fallback: Varsayılan tip
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `display_order` `order` INT NOT NULL DEFAULT 0");
                }

                // Index'leri yeniden oluştur
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    try {
                        $table->index('order', "idx_{$tableName}_order");
                    } catch (\Exception $e) {
                        // Index zaten varsa devam et
                    }
                });

                echo "✅ Rollback: {$tableName}.display_order → {$tableName}.order\n";
            }
        }
    }

    /**
     * Drop indexes for a specific column
     */
    private function dropIndexesForColumn(string $table, string $column): void
    {
        try {
            $indexes = DB::select("SHOW INDEXES FROM {$table} WHERE Column_name = ?", [$column]);
            foreach ($indexes as $index) {
                if ($index->Key_name !== 'PRIMARY') {
                    Schema::table($table, function (Blueprint $table) use ($index) {
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
