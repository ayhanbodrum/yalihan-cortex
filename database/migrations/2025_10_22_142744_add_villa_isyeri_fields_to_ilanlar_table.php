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
        Schema::table('ilanlar', function (Blueprint $table) {
            // Villa/Daire Eksik Alanları (2 field)
            $table->string('isinma_tipi')->nullable()->comment('Doğalgaz, Kombi, Klima, Soba, Merkezi, Yerden Isıtma');
            $table->json('site_ozellikleri')->nullable()->comment('Güvenlik, Otopark, Havuz, Spor, Sauna, Oyun Alanı, Asansör');
            
            // İşyeri Alanları (6 field)
            $table->string('isyeri_tipi')->nullable()->comment('Ofis, Mağaza, Dükkan, Depo, Fabrika, Atölye, Showroom');
            $table->text('kira_bilgisi')->nullable()->comment('Kira bilgileri');
            $table->decimal('ciro_bilgisi', 15, 2)->nullable()->comment('Aylık tahmini ciro');
            $table->string('ruhsat_durumu')->nullable()->comment('Var, Yok, Başvuruda');
            $table->integer('personel_kapasitesi')->nullable()->comment('Personel kapasitesi');
            $table->integer('isyeri_cephesi')->nullable()->comment('Cephe uzunluğu (metre)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Villa/Daire alanları
            $table->dropColumn(['isinma_tipi', 'site_ozellikleri']);
            
            // İşyeri alanları
            $table->dropColumn([
                'isyeri_tipi', 
                'kira_bilgisi', 
                'ciro_bilgisi', 
                'ruhsat_durumu', 
                'personel_kapasitesi', 
                'isyeri_cephesi'
            ]);
        });
    }
};
