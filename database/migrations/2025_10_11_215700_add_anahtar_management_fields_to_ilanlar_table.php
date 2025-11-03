<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7 Kural #68: Anahtar Yönetimi
     */
    public function up(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Önce anahtar_kimde varsa onu kontrol et, yoksa ekle
            if (!Schema::hasColumn('ilanlar', 'anahtar_kimde')) {
                $table->string('anahtar_kimde', 255)->nullable();
            }

            // Anahtar türü (enum)
            $table->enum('anahtar_turu', [
                'mal_sahibi',
                'danisman',
                'kapici',
                'emlakci',
                'yonetici',
                'diger'
            ])->nullable();

            // Anahtar notları (talimatlar)
            if (!Schema::hasColumn('ilanlar', 'anahtar_notlari')) {
                $table->text('anahtar_notlari')->nullable();
            }

            // Ulaşılabilirlik bilgisi
            $table->string('anahtar_ulasilabilirlik', 100)->nullable();

            // Ek bilgi (kapı kodu, alarm şifresi vb.)
            $table->string('anahtar_ek_bilgi', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            $table->dropColumn([
                'anahtar_turu',
                'anahtar_ulasilabilirlik',
                'anahtar_ek_bilgi'
            ]);
        });
    }
};
