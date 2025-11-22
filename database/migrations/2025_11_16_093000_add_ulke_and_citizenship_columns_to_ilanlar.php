<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            if (! Schema::hasColumn('ilanlar', 'ulke_id')) {
                $table->unsignedBigInteger('ulke_id')->nullable()->after('il_id');
            }
            if (! Schema::hasColumn('ilanlar', 'citizenship_eligible')) {
                $table->boolean('citizenship_eligible')->default(false)->after('ana_kategori_id');
            }
        });

        Schema::table('ilanlar', function (Blueprint $table) {
            $exists = function(string $name): bool {
                $rows = DB::select("SHOW INDEX FROM ilanlar WHERE Key_name = ?", [$name]);
                return !empty($rows);
            };
            if (Schema::hasColumn('ilanlar', 'ulke_id') && ! $exists('idx_ilanlar_ulke')) {
                $table->index('ulke_id', 'idx_ilanlar_ulke');
            }
            if (Schema::hasColumn('ilanlar', 'citizenship_eligible') && ! $exists('idx_ilanlar_citizenship')) {
                $table->index('citizenship_eligible', 'idx_ilanlar_citizenship');
            }
            if (Schema::hasColumn('ilanlar', 'ulke_id') && Schema::hasColumn('ilanlar', 'fiyat') && ! $exists('idx_ilanlar_ulke_fiyat')) {
                $table->index(['ulke_id','fiyat'], 'idx_ilanlar_ulke_fiyat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            if (Schema::hasColumn('ilanlar', 'citizenship_eligible')) {
                $table->dropIndex('idx_ilanlar_citizenship');
                $table->dropColumn('citizenship_eligible');
            }
            if (Schema::hasColumn('ilanlar', 'ulke_id')) {
                $table->dropIndex('idx_ilanlar_ulke');
                if (Schema::hasColumn('ilanlar', 'fiyat')) {
                    $table->dropIndex('idx_ilanlar_ulke_fiyat');
                }
                $table->dropColumn('ulke_id');
            }
        });
    }
};