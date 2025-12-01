<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add alt_kategori_id to talepler table
 *
 * Context7: Talep Model Reformu - 2025-11-24
 * SmartPropertyMatcherAI requires alt_kategori_id for matching
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('talepler')) {
            return;
        }

        Schema::table('talepler', function (Blueprint $table) {
            if (! Schema::hasColumn('talepler', 'alt_kategori_id')) {
                $table->unsignedBigInteger('alt_kategori_id')->nullable()->after('danisman_id')
                    ->comment('Context7: Alt kategori ID (IlanKategori)');

                $table->foreign('alt_kategori_id')
                    ->references('id')
                    ->on('ilan_kategorileri')
                    ->onDelete('set null');

                $table->index('alt_kategori_id', 'idx_talepler_alt_kategori');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('talepler')) {
            return;
        }

        Schema::table('talepler', function (Blueprint $table) {
            if (Schema::hasColumn('talepler', 'alt_kategori_id')) {
                $table->dropForeign(['alt_kategori_id']);
                $table->dropIndex('idx_talepler_alt_kategori');
                $table->dropColumn('alt_kategori_id');
            }
        });
    }
};


