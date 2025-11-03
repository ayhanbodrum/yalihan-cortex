<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ozellikler')) {
            return;
        }

        Schema::create('ozellikler', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('kategori_id')->nullable()->constrained('ozellik_kategorileri')->onDelete('set null');
            $table->string('veri_tipi')->default('text'); // text, number, boolean, select, multiselect
            $table->json('veri_secenekleri')->nullable();
            $table->string('birim')->nullable();
            $table->string('status')->default('Aktif');
            $table->integer('order')->default(0);
            $table->boolean('zorunlu')->default(false);
            $table->boolean('arama_filtresi')->default(false);
            $table->boolean('ilan_kartinda_goster')->default(false);
            $table->text('aciklama')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('kategori_id');
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ozellikler');
    }
};
