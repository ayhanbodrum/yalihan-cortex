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
        Schema::create('kisi_etkilesimler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kisi_id')->constrained('kisiler')->onDelete('cascade');
            $table->foreignId('kullanici_id')->constrained('users')->onDelete('cascade');
            $table->enum('tip', ['telefon', 'email', 'sms', 'toplanti', 'whatsapp', 'not']);
            $table->text('notlar')->nullable();
            $table->timestamp('etkilesim_tarihi');
            $table->tinyInteger('status')->default(1)->comment('1: aktif, 0: pasif');
            $table->integer('display_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['kisi_id', 'etkilesim_tarihi']);
            $table->index('kullanici_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kisi_etkilesimler');
    }
};
