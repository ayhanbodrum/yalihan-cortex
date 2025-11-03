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
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ilan_id')->constrained('ilanlar')->onDelete('cascade');
            
            // Sezon Tanımı (TatildeKirala/Airbnb tarzı)
            $table->string('name'); // Sezon adı (Yaz Sezonu, Ara Sezon, Kış Sezonu)
            $table->enum('type', [
                'yaz',          // Yaz Sezonu (Haziran-Eylül)
                'ara_sezon',    // Ara Sezon (Nisan-Mayıs, Ekim)
                'kis',          // Kış Sezonu (Kasım-Mart)
                'bayram',       // Bayram dönemi
                'ozel'          // Özel sezon (Yılbaşı, etc.)
            ]);
            
            $table->date('start_date'); // Başlangıç tarihi
            $table->date('end_date'); // Bitiş tarihi
            
            // Fiyatlandırma (TatildeKirala tarzı)
            $table->decimal('daily_price', 10, 2); // Günlük fiyat
            $table->decimal('weekly_price', 10, 2)->nullable(); // Haftalık fiyat (indirimli)
            $table->decimal('monthly_price', 10, 2)->nullable(); // Aylık fiyat (indirimli)
            $table->string('currency', 3)->default('TRY'); // Para birimi
            
            // Konaklama Kuralları
            $table->integer('minimum_stay')->default(1); // Minimum konaklama (gece)
            $table->integer('maximum_stay')->nullable(); // Maximum konaklama (opsiyonel)
            
            // Hafta içi/sonu Farkı (Opsiyonel)
            $table->decimal('weekend_price', 10, 2)->nullable(); // Hafta sonu fiyatı
            $table->boolean('weekend_pricing_enabled')->default(false); // Hafta sonu fiyat aktif mi?
            
            // Ek Ücretler
            $table->decimal('cleaning_fee', 10, 2)->default(0); // Temizlik ücreti
            $table->decimal('service_fee_percent', 5, 2)->default(0); // Hizmet bedeli %
            $table->integer('deposit_percent')->default(30); // Kapora oranı %
            
            // Durum
            $table->boolean('is_active')->default(true); // Aktif mi?
            $table->integer('priority')->default(0); // Öncelik (çakışmalarda)
            
            // Notlar
            $table->text('description')->nullable(); // Sezon açıklaması
            $table->text('special_conditions')->nullable(); // Özel şartlar
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index'ler
            $table->index('ilan_id');
            $table->index('type');
            $table->index('is_active');
            $table->index(['ilan_id', 'is_active']);
            $table->index(['start_date', 'end_date']); // Date range
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
