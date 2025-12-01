<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Talep Modeli Reformu - Alan Ekleme Migration
 *
 * Context7: Talep Modeli Reformu kapsamında eksik alanları ekler
 * - aranan_ozellikler_json: AI eşleştirme için JSON kolon
 * - min_metrekare, max_metrekare: Metrekare aralığı için integer kolonlar
 * - metadata: Genel metadata için JSON kolon
 *
 * Tarih: 24 Kasım 2025
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ✅ CONTEXT7: Tablo varlık kontrolü
        if (! Schema::hasTable('talepler')) {
            return;
        }

        Schema::table('talepler', function (Blueprint $table) {
            // Context7: Talep Modeli Reformu - 2025-11-24
            // min_metrekare, max_metrekare: Integer kolonlar (metrekare aralığı)
            if (! Schema::hasColumn('talepler', 'min_metrekare')) {
                $table->integer('min_metrekare')->nullable()->after('max_fiyat');
            }
            if (! Schema::hasColumn('talepler', 'max_metrekare')) {
                $table->integer('max_metrekare')->nullable()->after('min_metrekare');
            }

            // aranan_ozellikler_json: JSON kolon (AI eşleştirme için)
            if (! Schema::hasColumn('talepler', 'aranan_ozellikler_json')) {
                $table->json('aranan_ozellikler_json')->nullable()->after('notlar');
            }

            // metadata: JSON kolon (genel metadata için)
            if (! Schema::hasColumn('talepler', 'metadata')) {
                $table->json('metadata')->nullable()->after('aranan_ozellikler_json');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('talepler')) {
            return;
        }

        Schema::table('talepler', function (Blueprint $table) {
            // Context7: Talep Modeli Reformu - Rollback
            if (Schema::hasColumn('talepler', 'aranan_ozellikler_json')) {
                $table->dropColumn('aranan_ozellikler_json');
            }
            if (Schema::hasColumn('talepler', 'min_metrekare')) {
                $table->dropColumn('min_metrekare');
            }
            if (Schema::hasColumn('talepler', 'max_metrekare')) {
                $table->dropColumn('max_metrekare');
            }
            if (Schema::hasColumn('talepler', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });
    }
};
