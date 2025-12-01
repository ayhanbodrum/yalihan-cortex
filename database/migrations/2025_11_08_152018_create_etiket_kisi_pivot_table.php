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
        Schema::create('etiket_kisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etiket_id')->constrained('etiketler')->onDelete('cascade');
            $table->foreignId('kisi_id')->constrained('kisiler')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Etiketi ekleyen kullanıcı');
            $table->timestamps();

            // Unique constraint: Bir kişiye aynı etiket birden fazla kez eklenemez
            $table->unique(['etiket_id', 'kisi_id'], 'etiket_kisi_unique');

            // Indexes
            $table->index('etiket_id');
            $table->index('kisi_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etiket_kisi');
    }
};
