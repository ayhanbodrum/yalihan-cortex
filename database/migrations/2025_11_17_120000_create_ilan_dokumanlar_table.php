<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ilan_dokumanlar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ilan_id');
            $table->string('title');
            $table->string('type')->nullable();
            $table->string('url')->nullable();
            $table->string('path')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->foreign('ilan_id')->references('id')->on('ilanlar')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ilan_dokumanlar');
    }
};