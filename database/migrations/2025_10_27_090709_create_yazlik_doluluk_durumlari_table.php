<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yazlik_doluluk_durumlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ilan_id')->constrained('ilanlar')->cascadeOnDelete();
            $table->date('tarih');
            $table->enum('durum', ['musait', 'rezerve', 'bloke', 'bakim', 'temizlik', 'kapali'])->default('musait');
            $table->text('aciklama')->nullable();
            $table->json('ozel_fiyat')->nullable();
            $table->foreignId('rezervasyon_id')->nullable()->constrained('yazlik_rezervasyonlar')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['ilan_id', 'tarih']);
            $table->index(['ilan_id', 'durum']);
            $table->index(['ilan_id', 'tarih', 'durum']);
            $table->index(['tarih', 'durum']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yazlik_doluluk_durumlari');
    }
};
