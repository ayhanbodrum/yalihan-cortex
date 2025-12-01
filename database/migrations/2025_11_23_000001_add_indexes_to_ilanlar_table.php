<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            if (DB::getDriverName() !== 'mysql') {
                return;
            }

            $exists = function (string $name): bool {
                $rows = DB::select('SHOW INDEX FROM ilanlar WHERE Key_name = ?', [$name]);

                return ! empty($rows);
            };

            Schema::table('ilanlar', function (Blueprint $table) use ($exists) {
                if (Schema::hasColumn('ilanlar', 'status') && ! $exists('idx_ilanlar_status')) {
                    $table->index('status', 'idx_ilanlar_status');
                }
                if (Schema::hasColumn('ilanlar', 'il_id') && ! $exists('idx_ilanlar_il')) {
                    $table->index('il_id', 'idx_ilanlar_il');
                }
                if (Schema::hasColumn('ilanlar', 'ilce_id') && ! $exists('idx_ilanlar_ilce')) {
                    $table->index('ilce_id', 'idx_ilanlar_ilce');
                }
                if (Schema::hasColumn('ilanlar', 'alt_kategori_id') && ! $exists('idx_ilanlar_alt_kategori')) {
                    $table->index('alt_kategori_id', 'idx_ilanlar_alt_kategori');
                }
            });

            Schema::table('ilanlar', function (Blueprint $table) use ($exists) {
                if (Schema::hasColumn('ilanlar', 'il_id') && Schema::hasColumn('ilanlar', 'ilce_id') && ! $exists('idx_ilanlar_il_ilce')) {
                    $table->index(['il_id', 'ilce_id'], 'idx_ilanlar_il_ilce');
                }
                if (Schema::hasColumn('ilanlar', 'alt_kategori_id') && Schema::hasColumn('ilanlar', 'status') && ! $exists('idx_ilanlar_kategori_status')) {
                    $table->index(['alt_kategori_id', 'status'], 'idx_ilanlar_kategori_status');
                }
                if (Schema::hasColumn('ilanlar', 'fiyat') && ! $exists('idx_ilanlar_fiyat')) {
                    $table->index('fiyat', 'idx_ilanlar_fiyat');
                }
                if (Schema::hasColumn('ilanlar', 'created_at') && ! $exists('idx_ilanlar_created')) {
                    $table->index('created_at', 'idx_ilanlar_created');
                }
            });
        } catch (\Throwable $e) {
            // swallow
        }
    }

    public function down(): void
    {
        try {
            if (DB::getDriverName() !== 'mysql') {
                return;
            }
            $drop = function (string $name): void {
                try {
                    Schema::table('ilanlar', function (Blueprint $table) use ($name) {
                        $table->dropIndex($name);
                    });
                } catch (\Throwable $e) {
                }
            };

            foreach ([
                'idx_ilanlar_status',
                'idx_ilanlar_il',
                'idx_ilanlar_ilce',
                'idx_ilanlar_alt_kategori',
                'idx_ilanlar_il_ilce',
                'idx_ilanlar_kategori_status',
                'idx_ilanlar_fiyat',
                'idx_ilanlar_created',
            ] as $idx) {
                $drop($idx);
            }
        } catch (\Throwable $e) {
            // swallow
        }
    }
};
