<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iller', function (Blueprint $table) {
            $table->id();
            $table->string('il_adi');
            $table->string('plaka_kodu', 3);
            $table->string('telefon_kodu', 4)->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('il_adi');
            $table->unique('plaka_kodu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iller');
    }
};
