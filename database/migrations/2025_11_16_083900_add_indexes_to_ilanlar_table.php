<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            $exists = function (string $name): bool {
                $rows = DB::select('SHOW INDEX FROM ilanlar WHERE Key_name = ?', [$name]);

                return ! empty($rows);
            };
            if (Schema::hasColumn('ilanlar', 'status') && ! $exists('idx_ilanlar_status')) {
                $table->index('status', 'idx_ilanlar_status');
            }
            if (Schema::hasColumn('ilanlar', 'il_id') && ! $exists('idx_ilanlar_il')) {
                $table->index('il_id', 'idx_ilanlar_il');
            }
            if (Schema::hasColumn('ilanlar', 'ilce_id') && ! $exists('idx_ilanlar_ilce')) {
                $table->index('ilce_id', 'idx_ilanlar_ilce');
            }
            if (Schema::hasColumn('ilanlar', 'mahalle_id') && ! $exists('idx_ilanlar_mahalle')) {
                $table->index('mahalle_id', 'idx_ilanlar_mahalle');
            }
            if (Schema::hasColumn('ilanlar', 'ulke_id') && ! $exists('idx_ilanlar_ulke')) {
                $table->index('ulke_id', 'idx_ilanlar_ulke');
            }
            if (Schema::hasColumn('ilanlar', 'ana_kategori_id') && ! $exists('idx_ilanlar_ana_kategori')) {
                $table->index('ana_kategori_id', 'idx_ilanlar_ana_kategori');
            }
            if (Schema::hasColumn('ilanlar', 'yayin_tipi_id') && ! $exists('idx_ilanlar_yayin_tipi')) {
                $table->index('yayin_tipi_id', 'idx_ilanlar_yayin_tipi');
            }
            if (Schema::hasColumn('ilanlar', 'citizenship_eligible') && ! $exists('idx_ilanlar_citizenship')) {
                $table->index('citizenship_eligible', 'idx_ilanlar_citizenship');
            }
            if (Schema::hasColumn('ilanlar', 'fiyat') && ! $exists('idx_ilanlar_fiyat')) {
                $table->index('fiyat', 'idx_ilanlar_fiyat');
            }
            if (Schema::hasColumn('ilanlar', 'created_at') && ! $exists('idx_ilanlar_created_at')) {
                $table->index('created_at', 'idx_ilanlar_created_at');
            }
            if (
                Schema::hasColumn('ilanlar', 'status') &&
                Schema::hasColumn('ilanlar', 'il_id') &&
                Schema::hasColumn('ilanlar', 'ana_kategori_id') &&
                ! $exists('idx_ilanlar_status_il_kategori')
            ) {
                $table->index(['status', 'il_id', 'ana_kategori_id'], 'idx_ilanlar_status_il_kategori');
            }
            if (Schema::hasColumn('ilanlar', 'baslik') && Schema::hasColumn('ilanlar', 'aciklama')) {
                try {
                    DB::statement('ALTER TABLE ilanlar ADD FULLTEXT ft_ilanlar_baslik_aciklama (baslik, aciklama)');
                } catch (\Throwable $e) {
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            if (Schema::hasColumn('ilanlar', 'status')) {
                $table->dropIndex('idx_ilanlar_status');
            }
            if (Schema::hasColumn('ilanlar', 'il_id')) {
                $table->dropIndex('idx_ilanlar_il');
            }
            if (Schema::hasColumn('ilanlar', 'ilce_id')) {
                $table->dropIndex('idx_ilanlar_ilce');
            }
            if (Schema::hasColumn('ilanlar', 'mahalle_id')) {
                $table->dropIndex('idx_ilanlar_mahalle');
            }
            if (Schema::hasColumn('ilanlar', 'ulke_id')) {
                $table->dropIndex('idx_ilanlar_ulke');
            }
            if (Schema::hasColumn('ilanlar', 'ana_kategori_id')) {
                $table->dropIndex('idx_ilanlar_ana_kategori');
            }
            if (Schema::hasColumn('ilanlar', 'yayin_tipi_id')) {
                $table->dropIndex('idx_ilanlar_yayin_tipi');
            }
            if (Schema::hasColumn('ilanlar', 'citizenship_eligible')) {
                $table->dropIndex('idx_ilanlar_citizenship');
            }
            if (Schema::hasColumn('ilanlar', 'fiyat')) {
                $table->dropIndex('idx_ilanlar_fiyat');
            }
            if (Schema::hasColumn('ilanlar', 'created_at')) {
                $table->dropIndex('idx_ilanlar_created_at');
            }
            try {
                DB::statement('ALTER TABLE ilanlar DROP INDEX ft_ilanlar_baslik_aciklama');
            } catch (\Throwable $e) {
            }
        });
    }
};
