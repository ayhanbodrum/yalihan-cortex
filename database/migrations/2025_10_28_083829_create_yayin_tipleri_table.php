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
        if (Schema::hasTable('yayin_tipleri')) {
            return; // Tablo zaten var, migration'ı atla
        }

        Schema::create('yayin_tipleri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kategori_id')->index(); // Ana kategori ID
            $table->string('name'); // Satılık, Kiralık, Devren Satılık, vb.
            $table->string('slug')->unique();
            $table->boolean('status')->default(true); // Context7: enabled → status
            $table->timestamps();

            // ✅ CONTEXT7: Foreign key - categories → ilan_kategorileri (doğru tablo adı)
            if (Schema::hasTable('ilan_kategorileri')) {
                $table->foreign('kategori_id')->references('id')->on('ilan_kategorileri')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yayin_tipleri');
    }
};
