<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yazlik_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ilan_id')->unique()->constrained('ilanlar')->onDelete('cascade');

            $table->integer('min_konaklama')->default(1)->comment('Minimum konaklama günü');
            $table->integer('max_misafir')->nullable()->comment('Maksimum misafir sayısı');
            $table->decimal('temizlik_ucreti', 10, 2)->nullable();

            $table->boolean('havuz')->default(false);
            $table->string('havuz_turu')->nullable();
            $table->string('havuz_boyut')->nullable();
            $table->string('havuz_derinlik')->nullable();

            $table->decimal('gunluk_fiyat', 10, 2)->nullable();
            $table->decimal('haftalik_fiyat', 10, 2)->nullable();
            $table->decimal('aylik_fiyat', 10, 2)->nullable();
            $table->decimal('sezonluk_fiyat', 10, 2)->nullable();

            $table->date('sezon_baslangic')->nullable();
            $table->date('sezon_bitis')->nullable();

            $table->boolean('elektrik_dahil')->default(false);
            $table->boolean('su_dahil')->default(false);

            $table->text('ozel_notlar')->nullable();
            $table->text('musteri_notlari')->nullable();
            $table->text('indirim_notlari')->nullable();

            $table->decimal('indirimli_fiyat', 10, 2)->nullable();
            $table->string('anahtar_kimde')->nullable();
            $table->text('anahtar_notlari')->nullable();
            $table->text('sahip_ozel_notlari')->nullable();
            $table->string('sahip_iletisim_tercihi')->nullable();

            $table->boolean('eids_onayli')->default(false);
            $table->date('eids_onay_tarihi')->nullable();
            $table->string('eids_belge_no')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('ilan_id');
            $table->index('sezon_baslangic');
            $table->index('sezon_bitis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yazlik_details');
    }
};
