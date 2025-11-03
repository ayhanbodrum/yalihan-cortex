<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('eslesmeler')) {
            return;
        }

        Schema::create('eslesmeler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ilan_id')->constrained('ilanlar')->onDelete('cascade');
            $table->foreignId('kisi_id')->constrained('kisiler')->onDelete('cascade');
            $table->foreignId('talep_id')->nullable()->constrained('talepler')->onDelete('cascade');
            $table->foreignId('danisman_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('skor')->default(0); // 0-100 arası eşleşme skoru
            $table->string('status')->default('beklemede'); // beklemede, active, reddedildi, tamamlandi
            $table->text('notlar')->nullable();
            $table->json('eslesme_detaylari')->nullable(); // AI analiz sonuçları
            $table->timestamp('eslesme_tarihi')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('skor');
            $table->index(['ilan_id', 'kisi_id']);
            $table->index('talep_id');
            $table->index('danisman_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eslesmeler');
    }
};
