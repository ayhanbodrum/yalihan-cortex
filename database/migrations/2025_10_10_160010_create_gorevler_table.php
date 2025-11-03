<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gorevler', function (Blueprint $table) {
            $table->id();
            $table->string('baslik');
            $table->text('aciklama')->nullable();
            $table->string('status')->default('Beklemede');
            $table->string('oncelik')->default('Normal');
            $table->unsignedBigInteger('atanan_user_id')->nullable();
            $table->unsignedBigInteger('olusturan_user_id')->nullable();
            $table->date('baslangic_tarihi')->nullable();
            $table->date('bitis_tarihi')->nullable();
            $table->integer('tamamlanma_yuzdesi')->default(0);
            $table->text('notlar')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'oncelik']);
            $table->index('atanan_user_id');
            $table->index('bitis_tarihi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gorevler');
    }
};
