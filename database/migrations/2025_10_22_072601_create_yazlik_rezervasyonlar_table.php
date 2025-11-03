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
        Schema::create('yazlik_rezervasyonlar', function (Blueprint $table) {
            $table->id();

            // İlan ilişkisi
            $table->foreignId('ilan_id')->constrained('ilanlar')->onDelete('cascade')->comment('İlan ID');

            // Müşteri bilgileri
            $table->string('musteri_adi', 255)->comment('Müşteri adı');
            $table->string('musteri_telefon', 50)->comment('Müşteri telefonu');
            $table->string('musteri_email', 255)->nullable()->comment('Müşteri e-posta');

            // Rezervasyon tarihleri
            $table->date('check_in')->comment('Giriş tarihi');
            $table->date('check_out')->comment('Çıkış tarihi');

            // Misafir bilgileri
            $table->integer('misafir_sayisi')->default(1)->comment('Misafir sayısı');
            $table->integer('cocuk_sayisi')->default(0)->comment('Çocuk sayısı');
            $table->integer('pet_sayisi')->default(0)->comment('Evcil hayvan sayısı');

            // Özel istekler
            $table->text('ozel_istekler')->nullable()->comment('Özel istekler');

            // Finansal (Context7: para_birimi ilanlar tablosunda)
            $table->decimal('toplam_fiyat', 10, 2)->comment('Toplam fiyat');
            $table->decimal('kapora_tutari', 10, 2)->nullable()->comment('Kapora tutarı');

            // Status (Context7: enum için string değerler)
            $table->enum('status', ['beklemede', 'onaylandi', 'iptal', 'tamamlandi'])->default('beklemede')->comment('Rezervasyon durumu');

            // İptal bilgileri
            $table->text('iptal_nedeni')->nullable()->comment('İptal nedeni');

            // Onay tarihi
            $table->timestamp('onay_tarihi')->nullable()->comment('Onay tarihi');

            $table->timestamps();

            // Index'ler
            $table->index('ilan_id', 'idx_yazlik_rez_ilan');
            $table->index(['check_in', 'check_out'], 'idx_yazlik_rez_tarih');
            $table->index('status', 'idx_yazlik_rez_status');
            $table->index('musteri_telefon', 'idx_yazlik_rez_telefon');
            $table->index('musteri_email', 'idx_yazlik_rez_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yazlik_rezervasyonlar');
    }
};
