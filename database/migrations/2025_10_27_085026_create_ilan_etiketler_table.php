<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ilan_etiketler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ilan_id')->constrained('ilanlar')->cascadeOnDelete();
            $table->foreignId('etiket_id')->constrained('etiketler')->cascadeOnDelete();
            $table->integer('display_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->unique(['ilan_id', 'etiket_id']);
            $table->index(['ilan_id', 'display_order']);
            $table->index(['etiket_id', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ilan_etiketler');
    }
};
