<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 🔗 Alt Kategori ↔ Yayın Tipi İlişki Tablosu
     *
     * Bu tablo, hangi yayın tipinin hangi alt kategoride görüneceğini saklar.
     *
     * Örnek İlişkiler:
     * - Arsa > Konut İmarlı > [Satılık, Kiralık]
     * - Arsa > Ticari > [Satılık, Kiralık, Kat Karşılığı]
     */
    public function up(): void
    {
        Schema::create('alt_kategori_yayin_tipi', function (Blueprint $table) {
            $table->id();

            // ✅ Context7: Alt kategori (ilan_kategorileri tablosundan)
            $table->unsignedBigInteger('alt_kategori_id');
            $table->foreign('alt_kategori_id')
                ->references('id')
                ->on('ilan_kategorileri')
                ->onDelete('cascade');

            // ✅ Context7: Yayın tipi (ilan_kategori_yayin_tipleri tablosundan)
            $table->unsignedBigInteger('yayin_tipi_id');
            $table->foreign('yayin_tipi_id')
                ->references('id')
                ->on('ilan_kategori_yayin_tipleri')
                ->onDelete('cascade');

            // ✅ Context7: Aktif/Pasif durumu
            $table->boolean('status')->default(true); // Context7: enabled → status

            // ✅ Context7: Sıralama
            $table->integer('display_order')->default(0); // Context7: order → display_order

            $table->timestamps();

            // ✅ Unique constraint: Aynı ilişki birden fazla kez eklenemez
            $table->unique(['alt_kategori_id', 'yayin_tipi_id'], 'alt_kat_yayin_tipi_unique');

            // ✅ Index for performance
            $table->index(['alt_kategori_id', 'status']); // Context7: enabled → status
            $table->index('yayin_tipi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alt_kategori_yayin_tipi');
    }
};
