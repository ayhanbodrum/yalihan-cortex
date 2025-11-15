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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ilan_id')->constrained('ilanlar')->onDelete('cascade');
            $table->string('path'); // Orijinal fotoğraf yolu
            $table->string('thumbnail')->nullable(); // Küçük resim yolu
            $table->string('category')->default('genel'); // Kategori: genel, kat_plani, cephe, ic_mekan, vs
            $table->boolean('is_featured')->default(false); // Öne çıkan fotoğraf mı?
            $table->integer('display_order')->default(0); // Context7: order → display_order
            $table->integer('views')->default(0); // Görüntülenme sayısı
            $table->bigInteger('size')->nullable(); // Dosya boyutu (bytes)
            $table->string('mime_type')->nullable(); // Dosya tipi (image/jpeg, etc.)
            $table->integer('width')->nullable(); // Genişlik (px)
            $table->integer('height')->nullable(); // Yükseklik (px)
            $table->timestamps();
            $table->softDeletes(); // Soft delete desteği

            // Index'ler - performans için
            $table->index('ilan_id');
            $table->index('is_featured');
            $table->index('display_order'); // Context7: order → display_order (kolon photos tablosunda zaten display_order olmalı)
            $table->index('category');
            $table->index(['ilan_id', 'is_featured']); // Compound index
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
