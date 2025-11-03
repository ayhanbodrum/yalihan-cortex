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
        Schema::create('ilan_ozellikleri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ilan_id');
            $table->unsignedBigInteger('ozellik_id');
            $table->string('deger')->nullable()->comment('Özellik değeri');
            $table->text('aciklama')->nullable()->comment('Özellik açıklaması');
            $table->enum('status', ['Aktif', 'Pasif'])->default('Aktif')->comment('Özellik durumu');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('ilan_id')->references('id')->on('ilanlar')->onDelete('cascade');
            $table->foreign('ozellik_id')->references('id')->on('ozellikler')->onDelete('cascade');

            // Indexes
            $table->index(['ilan_id', 'ozellik_id'], 'idx_ilan_ozellikleri_ilan_ozellik');
            $table->index('status', 'idx_ilan_ozellikleri_status');
            $table->index('created_at', 'idx_ilan_ozellikleri_created_at');

            // Unique constraint - bir ilan için aynı özellik sadece bir kez olabilir
            $table->unique(['ilan_id', 'ozellik_id'], 'unique_ilan_ozellik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ilan_ozellikleri');
    }
};
