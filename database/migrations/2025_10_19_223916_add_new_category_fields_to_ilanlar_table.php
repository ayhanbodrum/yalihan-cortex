<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Yeni kategori sistemi alanları ekle
            $table->unsignedBigInteger('ana_kategori_id')->nullable()->after('kategori_id')->comment('Ana kategori ID');
            $table->unsignedBigInteger('alt_kategori_id')->nullable()->after('ana_kategori_id')->comment('Alt kategori ID');
            $table->unsignedBigInteger('yayin_tipi_id')->nullable()->after('alt_kategori_id')->comment('Yayın tipi ID');

            // Foreign key constraints ekle
            $table->foreign('ana_kategori_id')->references('id')->on('ilan_kategorileri')->onDelete('set null');
            $table->foreign('alt_kategori_id')->references('id')->on('ilan_kategorileri')->onDelete('set null');
            $table->foreign('yayin_tipi_id')->references('id')->on('ilan_kategorileri')->onDelete('set null');

            // Index'ler ekle
            $table->index('ana_kategori_id', 'idx_ilanlar_ana_kategori');
            $table->index('alt_kategori_id', 'idx_ilanlar_alt_kategori');
            $table->index('yayin_tipi_id', 'idx_ilanlar_yayin_tipi');
            $table->index(['ana_kategori_id', 'alt_kategori_id'], 'idx_ilanlar_kategori_combo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Foreign key'leri kaldır
            $table->dropForeign(['ana_kategori_id']);
            $table->dropForeign(['alt_kategori_id']);
            $table->dropForeign(['yayin_tipi_id']);

            // Index'leri kaldır
            $table->dropIndex('idx_ilanlar_ana_kategori');
            $table->dropIndex('idx_ilanlar_alt_kategori');
            $table->dropIndex('idx_ilanlar_yayin_tipi');
            $table->dropIndex('idx_ilanlar_kategori_combo');

            // Kolonları kaldır
            $table->dropColumn(['ana_kategori_id', 'alt_kategori_id', 'yayin_tipi_id']);
        });
    }
};
