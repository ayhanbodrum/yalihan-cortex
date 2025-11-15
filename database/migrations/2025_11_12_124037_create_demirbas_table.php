<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Context7 Compliance Migration Template
 *
 * ⚠️ CONTEXT7 PERMANENT STANDARDS:
 * - ALWAYS use 'display_order' field, NEVER use 'order'
 * - ALWAYS use 'status' field, NEVER use 'enabled', 'aktif', 'is_active'
 * - Pre-commit hook will BLOCK migrations with forbidden patterns
 * - This is a PERMANENT STANDARD - NO EXCEPTIONS
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('demirbas', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Demirbaş adı (örn: Buzdolabı, Çamaşır Makinesi)
            $table->string('slug')->nullable(); // URL-friendly slug
            $table->string('brand')->nullable(); // Marka (örn: Arçelik, Beko)
            $table->string('icon')->nullable(); // İkon (emoji veya icon class)
            $table->text('description')->nullable(); // Açıklama

            // ✅ Context7: Hiyerarşik yapı (kategori_id)
            $table->unsignedBigInteger('kategori_id')->index();
            $table->foreign('kategori_id')->references('id')->on('demirbas_kategoris')->onDelete('cascade');

            // ✅ Context7: Kategori ve yayın tipi filtreleme (opsiyonel - kategori seviyesinde de olabilir)
            $table->unsignedBigInteger('ilan_kategori_id')->nullable()->index()->comment('Hangi ilan kategorisi için geçerli (null = tümü)');
            $table->foreign('ilan_kategori_id')->references('id')->on('ilan_kategorileri')->onDelete('set null');

            $table->unsignedBigInteger('yayin_tipi_id')->nullable()->index()->comment('Hangi yayın tipi için geçerli (null = tümü)');
            $table->foreign('yayin_tipi_id')->references('id')->on('ilan_kategori_yayin_tipleri')->onDelete('set null');

            // ✅ CONTEXT7 PERMANENT STANDARD: Display Order
            $table->integer('display_order')->default(0)->comment('Sıralama (Context7: order → display_order)');

            // ✅ CONTEXT7 PERMANENT STANDARD: Status field MUST be TINYINT(1) boolean
            $table->tinyInteger('status')->default(1)->comment('0=inactive, 1=active (Context7 boolean - PERMANENT STANDARD)');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['kategori_id', 'status']);
            $table->index(['ilan_kategori_id', 'yayin_tipi_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demirbas');
    }
};
