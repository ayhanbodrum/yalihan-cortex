<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Context7: Eski status kolonlarını temizle (enabled, is_active, aktif → status)
     */
    public function up(): void
    {
        // Tablolar ve eski kolonları
        $tablesToClean = [
            // Hem status hem eski kolon var - sadece eski kolonu kaldır
            'blog_categories' => 'is_active',
            'blog_tags' => 'is_active',
            'eslesmeler' => 'aktif', // Not: eslesmeler'de durum ENUM var, aktif TINYINT var
            'etiketler' => 'aktif',
            'feature_categories' => 'is_active',
            'features' => 'is_active',
            'ilan_kategori_yayin_tipleri' => 'is_active',
            'ilan_kategorileri' => 'is_active',
            'users' => 'is_active',

            // Sadece eski kolon var - önce status ekle, veri taşı, sonra eski kolonu kaldır
            'ai_knowledge_base' => 'is_active',
            'arsa_detaylari' => 'aktif',
            'arsa_ozellikleri' => 'aktif',
            'categories' => 'is_active',
            'commission_rates' => 'is_active',
            'exchange_rates' => 'is_active',
            'expertise_areas' => 'is_active',
            'ilan_dinamik_ozellikler' => 'aktif',
            'ilan_takvim_sezonlar' => 'aktif',
            'ilceler' => 'aktif',
            'isyeri_ozellikleri' => 'aktif',
            'konut_ozellikleri' => 'aktif',
            'language_settings' => 'is_active',
            'etiketler' => 'aktif', // Context7: musteri_etiketler → etiketler
            'nearby_place_categories' => 'is_active',
            'ozellik_kategorileri' => 'aktif',
            'para_birimleri' => 'aktif',
            'people' => 'is_active',
            'property_types' => 'is_active',
            'iller' => 'aktif', // Context7: sehirler → iller
            'site_settings' => 'is_active',
            'tax_rates' => 'is_active',
            'turistik_ozellikleri' => 'aktif',
            'yazlik_fiyatlandirma' => 'aktif',
        ];

        foreach ($tablesToClean as $tableName => $oldColumn) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            // Status kolonu var mı kontrol et
            $hasStatus = Schema::hasColumn($tableName, 'status');
            $hasOldColumn = Schema::hasColumn($tableName, $oldColumn);

            if (!$hasOldColumn) {
                // Eski kolon yoksa atla
                continue;
            }

            if (!$hasStatus) {
                // Status kolonu yoksa ekle ve veri taşı
                Schema::table($tableName, function (Blueprint $table) use ($oldColumn) {
                    $table->boolean('status')->default(true)->after($oldColumn);
                });

                // Veri taşıma
                try {
                    DB::statement("UPDATE {$tableName} SET status = {$oldColumn} WHERE status IS NULL");
                } catch (\Exception $e) {
                    // Hata durumunda varsayılan değer
                    DB::statement("UPDATE {$tableName} SET status = 1 WHERE status IS NULL");
                }
            }

            // Index kontrolü ve kaldırma
            try {
                // Eski kolonun index'lerini kaldır
                $indexes = DB::select("SHOW INDEXES FROM {$tableName} WHERE Column_name = '{$oldColumn}'");
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
                // Index yoksa devam et
            }

            // Eski kolonu kaldır
            Schema::table($tableName, function (Blueprint $table) use ($oldColumn) {
                $table->dropColumn($oldColumn);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback için eski kolonları geri ekle (veri kaybı olabilir)
        $tablesToRestore = [
            'blog_categories' => ['is_active' => 'tinyint'],
            'blog_tags' => ['is_active' => 'tinyint'],
            'eslesmeler' => ['aktif' => 'tinyint'],
            'etiketler' => ['aktif' => 'tinyint'],
            'feature_categories' => ['is_active' => 'tinyint'],
            'features' => ['is_active' => 'tinyint'],
            'ilan_kategori_yayin_tipleri' => ['is_active' => 'tinyint'],
            'ilan_kategorileri' => ['is_active' => 'tinyint'],
            'users' => ['is_active' => 'tinyint'],
        ];

        foreach ($tablesToRestore as $tableName => $columns) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            foreach ($columns as $columnName => $columnType) {
                if (!Schema::hasColumn($tableName, $columnName)) {
                    Schema::table($tableName, function (Blueprint $table) use ($columnName, $columnType) {
                        if ($columnType === 'tinyint') {
                            $table->tinyInteger($columnName)->default(1)->after('status');
                        }
                    });

                    // Veri geri yükleme (status'ten)
                    if (Schema::hasColumn($tableName, 'status')) {
                        try {
                            DB::statement("UPDATE {$tableName} SET {$columnName} = status");
                        } catch (\Exception $e) {
                            // Hata durumunda varsayılan değer
                            DB::statement("UPDATE {$tableName} SET {$columnName} = 1");
                        }
                    }
                }
            }
        }
    }
};
