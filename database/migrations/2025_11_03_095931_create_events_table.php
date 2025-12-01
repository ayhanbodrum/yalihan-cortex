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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ilan_id')->constrained('ilanlar')->onDelete('cascade');

            // Rezervasyon Tarihleri (Airbnb/TatildeKirala tarzı)
            $table->date('check_in'); // Giriş tarihi
            $table->date('check_out'); // Çıkış tarihi
            $table->time('check_in_time')->nullable(); // Giriş saati (14:00)
            $table->time('check_out_time')->nullable(); // Çıkış saati (11:00)
            $table->integer('night_count')->default(1); // Gece sayısı

            // Misafir Bilgileri
            $table->string('guest_name'); // Misafir adı
            $table->string('guest_email'); // Email
            $table->string('guest_phone'); // Telefon
            $table->integer('guest_count')->default(1); // Yetişkin sayısı
            $table->integer('child_count')->default(0); // Çocuk sayısı
            $table->integer('infant_count')->default(0); // Bebek sayısı
            $table->integer('pet_count')->default(0); // Evcil hayvan sayısı

            // Fiyatlandırma (TatildeKirala tarzı)
            $table->decimal('daily_price', 10, 2); // Günlük fiyat
            $table->decimal('total_price', 10, 2); // Toplam fiyat
            $table->decimal('cleaning_fee', 10, 2)->default(0); // Temizlik ücreti
            $table->decimal('service_fee', 10, 2)->default(0); // Hizmet bedeli
            $table->decimal('deposit_amount', 10, 2)->default(0); // Kapora tutarı
            $table->decimal('paid_amount', 10, 2)->default(0); // Ödenen tutar
            $table->string('currency', 3)->default('TRY'); // Para birimi

            // Rezervasyon Durumu
            $table->enum('status', [
                'pending',      // Beklemede
                'confirmed',    // Onaylandı
                'cancelled',    // İptal edildi
                'completed',    // Tamamlandı
                'no_show',       // Gelmedi
            ])->default('pending');

            $table->enum('payment_status', [
                'unpaid',       // Ödenmedi
                'partial',      // Kısmi ödendi
                'paid',         // Tamamen ödendi
                'refunded',      // İade edildi
            ])->default('unpaid');

            // Ek Bilgiler
            $table->text('special_requests')->nullable(); // Özel istekler
            $table->text('notes')->nullable(); // İç notlar (admin)
            $table->text('cancellation_reason')->nullable(); // İptal nedeni
            $table->timestamp('confirmed_at')->nullable(); // Onay tarihi
            $table->timestamp('cancelled_at')->nullable(); // İptal tarihi

            // Source (nereden geldi?)
            $table->enum('source', [
                'website',      // Web sitesi
                'phone',        // Telefon
                'email',        // Email
                'airbnb',       // Airbnb
                'booking',      // Booking.com
                'admin',         // Admin panel
            ])->default('website');

            $table->timestamps();
            $table->softDeletes(); // Soft delete

            // Index'ler - performans
            $table->index('ilan_id');
            $table->index('status');
            $table->index('check_in');
            $table->index('check_out');
            $table->index(['ilan_id', 'status']); // Compound
            $table->index(['check_in', 'check_out']); // Date range queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
