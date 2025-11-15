<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projeler')) {
            return;
        }

        Schema::create('projeler', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('aciklama')->nullable();
            $table->string('status')->default('Aktif'); // Aktif, Tamamlandı, İptal, Askıda
            $table->string('oncelik')->default('Orta'); // Düşük, Orta, Yüksek, Acil
            $table->date('baslangic_tarihi')->nullable();
            $table->date('bitis_tarihi')->nullable();
            $table->foreignId('takim_lideri_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('butce', 15, 2)->nullable();
            $table->integer('tamamlanma_yuzdesi')->default(0);
            $table->text('notlar')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('oncelik');
            $table->index('takim_lideri_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projeler');
    }
};
