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
        Schema::create('ozellik_kategorileri', function (Blueprint $table) {
            $table->id();
            $table->string('kategori_adi', 100)->unique(); // Altyapı, Genel Özellikler, Manzara, Konum
            $table->string('kategori_slug', 100)->unique(); // altyapi, genel_ozellikler, manzara, konum
            $table->string('kategori_icon', 50)->nullable(); // 🏗️, 🌳, 🏔️, 📍
            $table->text('aciklama')->nullable();
            $table->integer('sira')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ozellik_kategorileri');
    }
};
