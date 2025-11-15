<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Context7 Compliance: order → display_order
 * 
 * Bu migration aşağıdaki tablolardaki `order` kolonlarını `display_order` olarak yeniden adlandırır:
 * - blog_categories.order → display_order
 * - etiketler.order → display_order
 * - ozellikler.order → display_order
 * - site_ozellikleri.order → display_order
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
            'blog_categories',
            'etiketler',
            'ozellikler',
            'site_ozellikleri',
        ];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            $hasOrder = Schema::hasColumn($tableName, 'order');
            $hasDisplayOrder = Schema::hasColumn($tableName, 'display_order');

            if ($hasOrder && !$hasDisplayOrder) {
                // 1. Index'leri kontrol et ve kaldır
                $this->dropIndexesForColumn($tableName, 'order');

                // 2. Kolon bilgilerini al
                $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'order'");
                if (!empty($columnInfo)) {
                    $col = $columnInfo[0];
                    $columnType = $col->Type;
                    $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                    $default = $col->Default !== null 
                        ? "DEFAULT '{$col->Default}'" 
                        : ($col->Null === 'YES' ? 'DEFAULT NULL' : 'DEFAULT 0');
                    
                    // 3. MySQL'de direkt SQL ile rename (Context7: DB::statement kullan)
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `order` `display_order` {$columnType} {$isNullable} {$default}");
                } else {
                    // Fallback: Varsayılan tip
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `order` `display_order` INT NOT NULL DEFAULT 0");
                }

                // 4. Index'leri yeniden oluştur
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    try {
                        $table->index('display_order', "idx_{$tableName}_display_order");
                    } catch (\Exception $e) {
                        // Index zaten varsa devam et
                    }
                });

                echo "✅ Renamed: {$tableName}.order → {$tableName}.display_order\n";
            } elseif ($hasOrder && $hasDisplayOrder) {
                // Her iki kolon da varsa, veriyi migrate et
                DB::statement("UPDATE `{$tableName}` SET display_order = COALESCE(display_order, `order`) WHERE display_order IS NULL OR display_order = 0");
                echo "⚠️ Both columns exist: {$tableName}. Migrated data from order to display_order\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'site_ozellikleri',
            'ozellikler',
            'etiketler',
            'blog_categories',
        ];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            $hasDisplayOrder = Schema::hasColumn($tableName, 'display_order');

            if ($hasDisplayOrder) {
                // Rollback: display_order → order
                $this->dropIndexesForColumn($tableName, 'display_order');

                $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'display_order'");
                if (!empty($columnInfo)) {
                    $col = $columnInfo[0];
                    $columnType = $col->Type;
                    $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                    $default = $col->Default !== null 
                        ? "DEFAULT '{$col->Default}'" 
                        : ($col->Null === 'YES' ? 'DEFAULT NULL' : 'DEFAULT 0');
                    
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `display_order` `order` {$columnType} {$isNullable} {$default}");
                } else {
                    DB::statement("ALTER TABLE `{$tableName}` CHANGE `display_order` `order` INT NOT NULL DEFAULT 0");
                }

                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    try {
                        $table->index('order', "idx_{$tableName}_order");
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
