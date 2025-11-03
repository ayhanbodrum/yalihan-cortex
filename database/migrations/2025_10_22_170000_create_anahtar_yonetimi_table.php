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
        Schema::create('anahtar_yonetimi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ilan_id'); // İlan ID
            $table->enum('anahtar_durumu', [
                'Beklemede',
                'Hazır',
                'Teslim Edildi',
                'Geri Alındı',
                'Kayıp'
            ])->default('Beklemede'); // Anahtar durumu
            $table->datetime('teslim_tarihi')->nullable(); // Teslim tarihi
            $table->unsignedBigInteger('teslim_eden_kisi_id')->nullable(); // Teslim eden kişi
            $table->unsignedBigInteger('teslim_alan_kisi_id')->nullable(); // Teslim alan kişi
            $table->string('anahtar_konumu')->nullable(); // Anahtar konumu
            $table->text('anahtar_notlari')->nullable(); // Anahtar notları
            $table->enum('anahtar_tipi', [
                'Ana Anahtar',
                'Yedek Anahtar',
                'Kodlu Anahtar',
                'Kartlı Anahtar',
                'Uzaktan Kumanda'
            ])->default('Ana Anahtar'); // Anahtar tipi
            $table->integer('anahtar_sayisi')->default(1); // Anahtar sayısı
            $table->json('anahtar_ozellikleri')->nullable(); // Anahtar özellikleri
            $table->enum('status', ['Aktif', 'Pasif', 'Silindi'])->default('Aktif'); // Durum
            $table->unsignedBigInteger('created_by')->nullable(); // Oluşturan
            $table->unsignedBigInteger('updated_by')->nullable(); // Güncelleyen
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('ilan_id')->references('id')->on('ilanlar')->onDelete('cascade');
            $table->foreign('teslim_eden_kisi_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('teslim_alan_kisi_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['ilan_id', 'anahtar_durumu']);
            $table->index(['teslim_tarihi']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anahtar_yonetimi');
    }
};
