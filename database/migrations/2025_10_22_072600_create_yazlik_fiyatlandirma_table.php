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
        Schema::create('yazlik_fiyatlandirma', function (Blueprint $table) {
            $table->id();
            
            // İlan ilişkisi
            $table->foreignId('ilan_id')->constrained('ilanlar')->onDelete('cascade')->comment('İlan ID');
            
            // Sezon tipi
            $table->enum('sezon_tipi', ['yaz', 'ara_sezon', 'kis'])->default('yaz')->comment('Sezon tipi');
            
            // Tarih aralığı
            $table->date('baslangic_tarihi')->comment('Sezon başlangıç tarihi');
            $table->date('bitis_tarihi')->comment('Sezon bitiş tarihi');
            
            // Fiyatlar (Context7: para_birimi ilanlar tablosunda)
            $table->decimal('gunluk_fiyat', 10, 2)->nullable()->comment('Günlük fiyat');
            $table->decimal('haftalik_fiyat', 10, 2)->nullable()->comment('Haftalık fiyat');
            $table->decimal('aylik_fiyat', 10, 2)->nullable()->comment('Aylık fiyat');
            
            // Konaklama kuralları
            $table->integer('minimum_konaklama')->default(1)->comment('Minimum konaklama günü');
            $table->integer('maksimum_konaklama')->nullable()->comment('Maksimum konaklama günü');
            
            // Özel günler (JSON)
            $table->json('ozel_gunler')->nullable()->comment('Özel günler ve fiyatları (JSON)');
            
            // Status (Context7: boolean active/enabled)
            $table->boolean('status')->default(true)->comment('Aktif mi? (true/false)');
            
            $table->timestamps();
            
            // Index'ler
            $table->index('ilan_id', 'idx_yazlik_fiyat_ilan');
            $table->index('sezon_tipi', 'idx_yazlik_fiyat_sezon');
            $table->index(['baslangic_tarihi', 'bitis_tarihi'], 'idx_yazlik_fiyat_tarih');
            $table->index('status', 'idx_yazlik_fiyat_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yazlik_fiyatlandirma');
    }
};
