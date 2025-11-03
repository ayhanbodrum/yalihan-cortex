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
        Schema::create('yayin_tipleri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kategori_id')->index(); // Ana kategori ID
            $table->string('name'); // Satılık, Kiralık, Devren Satılık, vb.
            $table->string('slug')->unique();
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            // Foreign key
            $table->foreign('kategori_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yayin_tipleri');
    }
};
