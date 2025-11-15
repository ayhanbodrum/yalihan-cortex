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
        Schema::create('ilan_demirbas', function (Blueprint $table) {
            $table->id();

            // ✅ Context7: İlan ve Demirbaş ilişkisi
            $table->unsignedBigInteger('ilan_id')->index();
            $table->foreign('ilan_id')->references('id')->on('ilanlar')->onDelete('cascade');

            $table->unsignedBigInteger('demirbas_id')->index();
            $table->foreign('demirbas_id')->references('id')->on('demirbas')->onDelete('cascade');

            // ✅ Context7: Ek bilgiler
            $table->string('brand')->nullable()->comment('Marka (ilan bazında override)');
            $table->string('model')->nullable()->comment('Model bilgisi');
            $table->integer('quantity')->default(1)->comment('Adet');
            $table->text('notes')->nullable()->comment('Notlar');

            // ✅ CONTEXT7 PERMANENT STANDARD: Display Order
            $table->integer('display_order')->default(0)->comment('Sıralama (Context7: order → display_order)');

            // ✅ CONTEXT7 PERMANENT STANDARD: Status field MUST be TINYINT(1) boolean
            $table->tinyInteger('status')->default(1)->comment('0=inactive, 1=active (Context7 boolean - PERMANENT STANDARD)');

            $table->timestamps();
            $table->softDeletes();

            // Unique constraint: Aynı ilan için aynı demirbaş sadece bir kez eklenebilir
            $table->unique(['ilan_id', 'demirbas_id'], 'unique_ilan_demirbas');

            // Indexes
            $table->index(['ilan_id', 'status']);
            $table->index(['demirbas_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ilan_demirbas');
    }
};
