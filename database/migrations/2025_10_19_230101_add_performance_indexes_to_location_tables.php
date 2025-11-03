<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Performance indexes for location tables
        $this->addLocationIndexes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->removeLocationIndexes();
    }

    /**
     * Add performance indexes
     */
    private function addLocationIndexes(): void
    {
        // İller tablosu indexleri
        Schema::table('iller', function (Blueprint $table) {
            if (!$this->indexExists('iller', 'idx_iller_ulke_id')) {
                $table->index('country_id', 'idx_iller_ulke_id');
            }
            if (!$this->indexExists('iller', 'idx_iller_plaka_kodu')) {
                $table->index('plaka_kodu', 'idx_iller_plaka_kodu');
            }
            if (!$this->indexExists('iller', 'idx_iller_status')) {
                $table->index('status', 'idx_iller_status');
            }
            if (!$this->indexExists('iller', 'idx_iller_coordinates')) {
                $table->index(['lat', 'lng'], 'idx_iller_coordinates');
            }
        });

        // İlçeler tablosu indexleri
        Schema::table('ilceler', function (Blueprint $table) {
            if (!$this->indexExists('ilceler', 'idx_ilceler_il_id')) {
                $table->index('il_id', 'idx_ilceler_il_id');
            }
            if (!$this->indexExists('ilceler', 'idx_ilceler_status')) {
                $table->index('status', 'idx_ilceler_status');
            }
            if (!$this->indexExists('ilceler', 'idx_ilceler_coordinates')) {
                $table->index(['lat', 'lng'], 'idx_ilceler_coordinates');
            }
            if (!$this->indexExists('ilceler', 'idx_ilceler_ilce_kodu')) {
                $table->index('ilce_kodu', 'idx_ilceler_ilce_kodu');
            }
        });

        // Mahalleler tablosu indexleri
        Schema::table('mahalleler', function (Blueprint $table) {
            if (!$this->indexExists('mahalleler', 'idx_mahalleler_ilce_id')) {
                $table->index('ilce_id', 'idx_mahalleler_ilce_id');
            }
            if (!$this->indexExists('mahalleler', 'idx_mahalleler_status')) {
                $table->index('status', 'idx_mahalleler_status');
            }
            if (!$this->indexExists('mahalleler', 'idx_mahalleler_coordinates')) {
                $table->index(['enlem', 'boylam'], 'idx_mahalleler_coordinates');
            }
            if (!$this->indexExists('mahalleler', 'idx_mahalleler_mahalle_kodu')) {
                $table->index('mahalle_kodu', 'idx_mahalleler_mahalle_kodu');
            }
        });

        // Ülkeler tablosu indexleri
        Schema::table('ulkeler', function (Blueprint $table) {
            if (!$this->indexExists('ulkeler', 'idx_ulkeler_ulke_kodu')) {
                $table->index('ulke_kodu', 'idx_ulkeler_ulke_kodu');
            }
            if (!$this->indexExists('ulkeler', 'idx_ulkeler_status')) {
                $table->index('status', 'idx_ulkeler_status');
            }
        });
    }

    /**
     * Remove performance indexes
     */
    private function removeLocationIndexes(): void
    {
        Schema::table('iller', function (Blueprint $table) {
            $table->dropIndex('idx_iller_ulke_id');
            $table->dropIndex('idx_iller_plaka_kodu');
            $table->dropIndex('idx_iller_status');
            $table->dropIndex('idx_iller_coordinates');
        });

        Schema::table('ilceler', function (Blueprint $table) {
            $table->dropIndex('idx_ilceler_il_id');
            $table->dropIndex('idx_ilceler_status');
            $table->dropIndex('idx_ilceler_coordinates');
            $table->dropIndex('idx_ilceler_ilce_kodu');
        });

        Schema::table('mahalleler', function (Blueprint $table) {
            $table->dropIndex('idx_mahalleler_ilce_id');
            $table->dropIndex('idx_mahalleler_status');
            $table->dropIndex('idx_mahalleler_coordinates');
            $table->dropIndex('idx_mahalleler_mahalle_kodu');
        });

        Schema::table('ulkeler', function (Blueprint $table) {
            $table->dropIndex('idx_ulkeler_ulke_kodu');
            $table->dropIndex('idx_ulkeler_status');
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
