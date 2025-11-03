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
        Schema::create('ozellik_alt_kategorileri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ozellik_kategori_id')->constrained('ozellik_kategorileri')->onDelete('cascade');
            $table->string('alt_kategori_adi', 100); // Elektrik, Su, Bahçe, Deniz, vs.
            $table->string('alt_kategori_slug', 100); // elektrik, su, bahce, deniz
            $table->string('alt_kategori_icon', 50)->nullable(); // ⚡, 💧, 🌳, 🌊
            $table->enum('field_type', ['text', 'number', 'boolean', 'select', 'textarea', 'date', 'price', 'location'])->default('text');
            $table->json('field_options')->nullable(); // Select options
            $table->string('field_unit', 20)->nullable(); // m², ₺, km, vs.
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
        Schema::dropIfExists('ozellik_alt_kategorileri');
    }
};
