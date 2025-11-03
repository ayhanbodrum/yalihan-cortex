<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ilan_kategori_yayin_tipleri')) {
            return;
        }

        Schema::create('ilan_kategori_yayin_tipleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('ilan_kategorileri')->onDelete('cascade');
            $table->string('yayin_tipi'); // 'Satılık', 'Kiralık', 'Günlük Kiralık', 'Devren Satılık'
            $table->string('status')->default('Aktif'); // Aktif, Pasif
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['kategori_id', 'yayin_tipi']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ilan_kategori_yayin_tipleri');
    }
};
