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
            if (Schema::hasColumn('ilanlar', 'ana_kategori_id') && Schema::hasColumn('ilanlar', 'il_id') && ! $exists('idx_ilanlar_ana_il')) {
                $table->index(['ana_kategori_id', 'il_id'], 'idx_ilanlar_ana_il');
            }
            if (Schema::hasColumn('ilanlar', 'ulke_id') && Schema::hasColumn('ilanlar', 'fiyat') && ! $exists('idx_ilanlar_ulke_fiyat')) {
                $table->index(['ulke_id', 'fiyat'], 'idx_ilanlar_ulke_fiyat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            $table->dropIndex('idx_ilanlar_ana_il');
            $table->dropIndex('idx_ilanlar_ulke_fiyat');
        });
    }
};
