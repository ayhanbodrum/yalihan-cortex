<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Context7 Compliance: musteri_* → kisi_*
 *
 * Bu migration aşağıdaki tablolardaki `musteri_*` kolonlarını `kisi_*` olarak yeniden adlandırır:
 * - yazlik_details.musteri_notlari → kisi_notlari
 * - yazlik_rezervasyonlar.musteri_adi → kisi_adi
 * - yazlik_rezervasyonlar.musteri_email → kisi_email
 * - yazlik_rezervasyonlar.musteri_telefon → kisi_telefon
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
        // yazlik_details.musteri_notlari → kisi_notlari
        if (Schema::hasTable('yazlik_details') && Schema::hasColumn('yazlik_details', 'musteri_notlari') && ! Schema::hasColumn('yazlik_details', 'kisi_notlari')) {
            $this->dropIndexesForColumn('yazlik_details', 'musteri_notlari');

            $columnInfo = DB::select("SHOW COLUMNS FROM `yazlik_details` WHERE Field = 'musteri_notlari'");
            if (! empty($columnInfo)) {
                $col = $columnInfo[0];
                $columnType = $col->Type;
                $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $col->Default !== null ? "DEFAULT '{$col->Default}'" : ($col->Null === 'YES' ? 'DEFAULT NULL' : '');

                DB::statement("ALTER TABLE `yazlik_details` CHANGE `musteri_notlari` `kisi_notlari` {$columnType} {$isNullable} {$default}");
                echo "✅ Renamed: yazlik_details.musteri_notlari → yazlik_details.kisi_notlari\n";
            }
        }

        // yazlik_rezervasyonlar kolonları
        if (Schema::hasTable('yazlik_rezervasyonlar')) {
            $columns = [
                ['old' => 'musteri_adi', 'new' => 'kisi_adi', 'type' => 'VARCHAR(255)'],
                ['old' => 'musteri_email', 'new' => 'kisi_email', 'type' => 'VARCHAR(255)'],
                ['old' => 'musteri_telefon', 'new' => 'kisi_telefon', 'type' => 'VARCHAR(50)'],
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('yazlik_rezervasyonlar', $column['old']) && ! Schema::hasColumn('yazlik_rezervasyonlar', $column['new'])) {
                    $this->dropIndexesForColumn('yazlik_rezervasyonlar', $column['old']);

                    $columnInfo = DB::select("SHOW COLUMNS FROM `yazlik_rezervasyonlar` WHERE Field = '{$column['old']}'");
                    if (! empty($columnInfo)) {
                        $col = $columnInfo[0];
                        $columnType = $col->Type;
                        $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                        $default = $col->Default !== null ? "DEFAULT '{$col->Default}'" : ($col->Null === 'YES' ? 'DEFAULT NULL' : '');

                        DB::statement("ALTER TABLE `yazlik_rezervasyonlar` CHANGE `{$column['old']}` `{$column['new']}` {$columnType} {$isNullable} {$default}");
                        echo "✅ Renamed: yazlik_rezervasyonlar.{$column['old']} → yazlik_rezervasyonlar.{$column['new']}\n";
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // yazlik_rezervasyonlar kolonları (ters sıra)
        if (Schema::hasTable('yazlik_rezervasyonlar')) {
            $columns = [
                ['old' => 'kisi_telefon', 'new' => 'musteri_telefon'],
                ['old' => 'kisi_email', 'new' => 'musteri_email'],
                ['old' => 'kisi_adi', 'new' => 'musteri_adi'],
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('yazlik_rezervasyonlar', $column['old'])) {
                    $this->dropIndexesForColumn('yazlik_rezervasyonlar', $column['old']);

                    $columnInfo = DB::select("SHOW COLUMNS FROM `yazlik_rezervasyonlar` WHERE Field = '{$column['old']}'");
                    if (! empty($columnInfo)) {
                        $col = $columnInfo[0];
                        $columnType = $col->Type;
                        $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                        $default = $col->Default !== null ? "DEFAULT '{$col->Default}'" : ($col->Null === 'YES' ? 'DEFAULT NULL' : '');

                        DB::statement("ALTER TABLE `yazlik_rezervasyonlar` CHANGE `{$column['old']}` `{$column['new']}` {$columnType} {$isNullable} {$default}");
                    }
                }
            }
        }

        // yazlik_details.kisi_notlari → musteri_notlari
        if (Schema::hasTable('yazlik_details') && Schema::hasColumn('yazlik_details', 'kisi_notlari')) {
            $this->dropIndexesForColumn('yazlik_details', 'kisi_notlari');

            $columnInfo = DB::select("SHOW COLUMNS FROM `yazlik_details` WHERE Field = 'kisi_notlari'");
            if (! empty($columnInfo)) {
                $col = $columnInfo[0];
                $columnType = $col->Type;
                $isNullable = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $col->Default !== null ? "DEFAULT '{$col->Default}'" : ($col->Null === 'YES' ? 'DEFAULT NULL' : '');

                DB::statement("ALTER TABLE `yazlik_details` CHANGE `kisi_notlari` `musteri_notlari` {$columnType} {$isNullable} {$default}");
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
