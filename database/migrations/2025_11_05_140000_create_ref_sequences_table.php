<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7 Standardı: C7-REF-SEQUENCE-2025-11-05
     * 
     * Sequence tablosu - Benzersiz referans numarası üretimi için
     * Format: {PREFIX}-{YAYINTIPI}-{LOKASYON}-{KATEGORI}-{SIRANO}
     */
    public function up(): void
    {
        Schema::create('ref_sequences', function (Blueprint $table) {
            $table->id();
            
            // Sequence key (unique identifier)
            // Format: {yayin_tipi}-{lokasyon_kodu}-{kategori_kodu}-{yil}
            // Örnek: SAT-YALKVK-DAIRE-2025
            $table->string('sequence_key', 100)->unique();
            
            // Son kullanılan sıra numarası
            $table->unsignedInteger('last_sequence')->default(0);
            
            // Yıl (sequence reset için)
            $table->year('year')->nullable();
            
            // Metadata
            $table->string('yayin_tipi', 10)->nullable(); // SAT, KİR, GÜN, DEV
            $table->string('lokasyon_kodu', 20)->nullable();
            $table->string('kategori_kodu', 20)->nullable();
            
            // Timestamps
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('sequence_key');
            $table->index(['yayin_tipi', 'lokasyon_kodu', 'kategori_kodu', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_sequences');
    }
};

