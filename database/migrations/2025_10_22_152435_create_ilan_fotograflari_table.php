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
        Schema::create('ilan_fotograflari', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ilan_id');
            $table->string('dosya_adi');
            $table->string('dosya_yolu');
            $table->string('dosya_boyutu')->nullable();
            $table->string('mime_type')->nullable();
            $table->boolean('kapak_fotografi')->default(false);
            $table->integer('sira')->default(0);
            $table->text('aciklama')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ilan_id')->references('id')->on('ilanlar')->onDelete('cascade');
            $table->index(['ilan_id', 'kapak_fotografi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ilan_fotograflari');
    }
};
