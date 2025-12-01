<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Context7 Compliance: sync_enabled → sync_status migration
 */
return new class extends Migration
{
    /**
     * Context7 Compliance: sync_enabled → sync_status migration
     */
    public function up(): void
    {
        if (! Schema::hasTable('ilan_takvim_sync')) {
            return;
        }

        // Context7 Compliance: sync_enabled → sync_status
        if (Schema::hasColumn('ilan_takvim_sync', 'sync_enabled')) {
            Schema::table('ilan_takvim_sync', function (Blueprint $table) {
                // Yeni sync_status kolonu ekle
                $table->string('sync_status_field', 20)->default('Aktif')->after('platform');
            });

            // Veriyi migrate et
            DB::statement("
                UPDATE ilan_takvim_sync
                SET sync_status_field = CASE
                    WHEN sync_enabled = 1 THEN 'Aktif'
                    ELSE 'Pasif'
                END
            ");

            Schema::table('ilan_takvim_sync', function (Blueprint $table) {
                // Eski sync_enabled kolonu sil
                $table->dropColumn('sync_enabled');

                // Kolon adını düzelt
                $table->renameColumn('sync_status_field', 'sync_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('ilan_takvim_sync')) {
            return;
        }

        if (Schema::hasColumn('ilan_takvim_sync', 'sync_status')) {
            Schema::table('ilan_takvim_sync', function (Blueprint $table) {
                // Boolean field geri ekle
                $table->boolean('sync_enabled')->default(true)->after('platform');
            });

            // Veriyi geri migrate et
            DB::statement("
                UPDATE ilan_takvim_sync
                SET sync_enabled = CASE
                    WHEN sync_status = 'Aktif' THEN 1
                    ELSE 0
                END
            ");

            Schema::table('ilan_takvim_sync', function (Blueprint $table) {
                $table->dropColumn('sync_status');
            });
        }
    }
};
