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
        Schema::create('mahalleler', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ilce_id');
            $table->string('mahalle_adi');
            $table->string('mahalle_kodu')->nullable();
            $table->string('posta_kodu')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ilce_id')->references('id')->on('ilceler')->onDelete('cascade');
            $table->index('ilce_id');
            $table->index('mahalle_adi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahalleler');
    }
};
