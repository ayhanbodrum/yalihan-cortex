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
        Schema::create('kisi_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kisi_id')->constrained('kisiler')->onDelete('cascade');
            $table->foreignId('kullanici_id')->constrained('users')->onDelete('cascade');
            $table->string('baslik');
            $table->text('aciklama')->nullable();
            $table->date('tarih');
            $table->time('saat')->nullable();
            $table->enum('oncelik', ['dusuk', 'normal', 'yuksek', 'kritik'])->default('normal');
            $table->tinyInteger('status')->default(0)->comment('0: beklemede, 1: tamamlandi');
            $table->timestamp('tamamlanma_tarihi')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['kisi_id', 'tarih']);
            $table->index(['kullanici_id', 'status']);
            $table->index('tarih');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kisi_tasks');
    }
};
