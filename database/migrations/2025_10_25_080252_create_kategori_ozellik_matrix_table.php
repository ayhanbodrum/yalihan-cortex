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
        Schema::create('kategori_ozellik_matrix', function (Blueprint $table) {
            $table->id();
            $table->string('kategori_slug', 50); // konut, arsa, yazlik, isyeri
            $table->string('yayin_tipi', 50); // Satılık, Kiralık, Sezonluk Kiralık, Devren Satış
            $table->foreignId('ozellik_kategori_id')->constrained('ozellik_kategorileri')->onDelete('cascade');
            $table->foreignId('ozellik_alt_kategori_id')->constrained('ozellik_alt_kategorileri')->onDelete('cascade');
            $table->boolean('aktif')->default(true);
            $table->boolean('zorunlu')->default(false);
            $table->boolean('ai_suggestion')->default(false);
            $table->boolean('ai_auto_fill')->default(false);
            $table->integer('sira')->default(0);
            $table->timestamps();

            // Unique constraint
            $table->unique(['kategori_slug', 'yayin_tipi', 'ozellik_kategori_id', 'ozellik_alt_kategori_id'], 'unique_matrix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_ozellik_matrix');
    }
};
