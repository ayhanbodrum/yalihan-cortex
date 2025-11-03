<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulkeler', function (Blueprint $table) {
            $table->id();
            $table->string('ulke_adi');
            $table->string('ulke_kodu', 3)->unique();
            $table->string('telefon_kodu', 10)->nullable();
            $table->string('para_birimi', 3)->nullable();
            $table->string('status')->default('Aktif');
            $table->timestamps();

            $table->index('ulke_adi');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulkeler');
    }
};
