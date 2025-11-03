<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kisiler', function (Blueprint $table) {
            $table->id();
            $table->string('ad');
            $table->string('soyad');
            $table->string('email')->nullable();
            $table->string('telefon')->nullable();
            $table->string('telefon_2')->nullable();
            $table->string('tc_kimlik', 11)->nullable();
            $table->text('adres')->nullable();
            $table->unsignedBigInteger('il_id')->nullable();
            $table->unsignedBigInteger('ilce_id')->nullable();
            $table->string('meslek')->nullable();
            $table->string('kisi_tipi')->default('Müşteri');
            $table->string('status')->default('Aktif');
            $table->text('notlar')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'kisi_tipi']);
            $table->index(['il_id', 'ilce_id']);
            $table->index('user_id');
            $table->index('email');
            $table->index('telefon');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kisiler');
    }
};
