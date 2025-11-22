<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ilan_goruntulenme_gunluk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ilan_id')->constrained('ilanlar')->cascadeOnDelete();
            $table->date('tarih');
            $table->string('cihaz', 20)->default('desktop');
            $table->unsignedInteger('adet')->default(0);
            $table->timestamps();
            $table->unique(['ilan_id', 'tarih', 'cihaz']);
            $table->index(['tarih']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ilan_goruntulenme_gunluk');
    }
};