<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ilceler', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('il_id');
            $table->string('ilce_adi');
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->timestamps();

            $table->index(['il_id', 'ilce_adi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ilceler');
    }
};
