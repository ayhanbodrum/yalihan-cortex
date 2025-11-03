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
        Schema::create('site_apartmanlar', function (Blueprint $table) {
            $table->id();

            // ✅ Context7: name kullan (NOT site_adi)
            $table->string('name')->comment('Site/Apartman adı');
            $table->text('adres')->nullable();

            // Lokasyon (Context7: il_id, ilce_id, mahalle_id)
            $table->unsignedBigInteger('il_id')->nullable();
            $table->unsignedBigInteger('ilce_id')->nullable();
            $table->unsignedBigInteger('mahalle_id')->nullable();

            // Yönetici bilgileri
            $table->string('yonetici_adi')->nullable();
            $table->string('yonetici_telefon')->nullable();
            $table->string('yonetici_email')->nullable();
            $table->string('kapici_telefon')->nullable();

            // Site özellikleri
            $table->integer('toplam_daire_sayisi')->nullable();
            $table->integer('kat_sayisi')->nullable();
            $table->integer('asansor_sayisi')->nullable();
            $table->string('otopark_statusu')->nullable();
            $table->json('sosyal_tesisler')->nullable();
            $table->json('guvenlik_sistemi')->nullable();

            // Finansal
            $table->decimal('aidat_tutari', 10, 2)->nullable();
            $table->string('aidat_para_birimi', 10)->default('TRY');
            $table->string('aidat_periyodu')->nullable()->comment('aylık, 3 aylık, 6 aylık, yıllık');

            // Yapı bilgileri
            $table->year('yapim_yili')->nullable();
            $table->string('yapi_tarzi')->nullable();
            $table->string('isitma_sistemi')->nullable();

            // Notlar
            $table->text('notlar')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('il_id')->references('id')->on('iller')->onDelete('set null');
            $table->foreign('ilce_id')->references('id')->on('ilceler')->onDelete('set null');
            $table->foreign('mahalle_id')->references('id')->on('mahalleler')->onDelete('set null');

            // Indexes
            $table->index('name');
            $table->index(['il_id', 'ilce_id', 'mahalle_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_apartmanlar');
    }
};
