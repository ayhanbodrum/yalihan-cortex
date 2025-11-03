<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Context7 Compliance: %100
     * Yalıhan Bekçi: ✅ Uyumlu
     * 
     * Arsa için kritik eksik field'lar ekleniyor:
     * - cephe_sayisi, ifraz_durumu, tapu_durumu, yol_durumu
     * - ifrazsiz, kat_karsiligi (boolean flags)
     */
    public function up(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Cephe bilgisi
            $table->string('cephe_sayisi', 20)->nullable()->after('gabari')
                ->comment('Cephe sayısı: 1 cephe, 2 cephe, 3 cephe, 4 cephe');
            
            // İfraz durumu
            $table->string('ifraz_durumu', 50)->nullable()->after('cephe_sayisi')
                ->comment('İfraz durumu: İfraza Uygun, İfrazsız, İfrazlı');
            
            // Tapu durumu
            $table->string('tapu_durumu', 50)->nullable()->after('ifraz_durumu')
                ->comment('Tapu durumu: Tapulu, Tahsisli, Kat Mülkiyetli, Hisseli');
            
            // Yol durumu
            $table->string('yol_durumu', 50)->nullable()->after('tapu_durumu')
                ->comment('Yol durumu: Asfalt, Stabilize, Toprak, Beton');
            
            // İfrazsız satılık (boolean flag)
            $table->boolean('ifrazsiz')->default(false)->after('yol_durumu')
                ->comment('İfrazsız satılabilir mi?');
            
            // Kat karşılığı (boolean flag)
            $table->boolean('kat_karsiligi')->default(false)->after('ifrazsiz')
                ->comment('Kat karşılığı satılık mı?');
            
            // Indexes (arama performansı için)
            $table->index('ifraz_durumu', 'idx_ilanlar_ifraz_durumu');
            $table->index('tapu_durumu', 'idx_ilanlar_tapu_durumu');
            $table->index(['ifrazsiz', 'kat_karsiligi'], 'idx_ilanlar_arsa_flags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_ilanlar_ifraz_durumu');
            $table->dropIndex('idx_ilanlar_tapu_durumu');
            $table->dropIndex('idx_ilanlar_arsa_flags');
            
            // Drop columns
            $table->dropColumn([
                'cephe_sayisi',
                'ifraz_durumu',
                'tapu_durumu',
                'yol_durumu',
                'ifrazsiz',
                'kat_karsiligi',
            ]);
        });
    }
};

