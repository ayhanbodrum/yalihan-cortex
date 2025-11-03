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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index(); // Site/apartman adı
            $table->string('blok_adi')->nullable(); // Blok adı (A Blok, B Blok vb.)
            $table->text('adres')->nullable(); // Detaylı adres

            // Lokasyon bilgileri (Context7 standart)
            $table->unsignedBigInteger('il_id');
            $table->unsignedBigInteger('ilce_id');
            $table->unsignedBigInteger('mahalle_id')->nullable();

            // Site durumu
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');

            // Oluşturan kullanıcı
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('il_id')->references('id')->on('iller')->onDelete('cascade');
            $table->foreign('ilce_id')->references('id')->on('ilceler')->onDelete('cascade');
            $table->foreign('mahalle_id')->references('id')->on('mahalleler')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for performance
            $table->index(['il_id', 'ilce_id', 'name']);
            $table->index(['status', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
