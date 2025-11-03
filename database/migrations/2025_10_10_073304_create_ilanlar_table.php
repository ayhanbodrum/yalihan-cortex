<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ilanlar', function (Blueprint $table) {
            $table->id();
            $table->string('baslik');
            $table->text('aciklama')->nullable();
            $table->decimal('fiyat', 15, 2)->nullable();
            $table->string('status')->default('Aktif');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->unsignedBigInteger('il_id')->nullable();
            $table->unsignedBigInteger('ilce_id')->nullable();
            $table->unsignedBigInteger('mahalle_id')->nullable();
            $table->string('adres')->nullable();
            $table->integer('oda_sayisi')->nullable();
            $table->integer('salon_sayisi')->nullable();
            $table->integer('banyo_sayisi')->nullable();
            $table->integer('kat')->nullable();
            $table->integer('toplam_kat')->nullable();
            $table->decimal('brut_m2', 10, 2)->nullable();
            $table->decimal('net_m2', 10, 2)->nullable();
            $table->year('bina_yasi')->nullable();
            $table->string('isitma')->nullable();
            $table->string('aidat')->nullable();
            $table->boolean('esyali')->default(false);
            $table->string('ilan_no')->unique()->nullable();
            $table->integer('goruntulenme')->default(0);
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['il_id', 'ilce_id']);
            $table->index(['kategori_id', 'status']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ilanlar');
    }
};
