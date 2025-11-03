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
     * Konut için kritik eksik field'lar ekleniyor:
     * - tapu_tipi (Kat Mülkiyeti, Arsa, Hisseli)
     * - krediye_uygun (boolean)
     */
    public function up(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Tapu tipi
            $table->string('tapu_tipi', 50)->nullable()->after('tapu_durumu')
                ->comment('Tapu tipi: Kat Mülkiyeti, Arsa, Hisseli, Kat İrtifakı');
            
            // Krediye uygunluk
            $table->boolean('krediye_uygun')->default(false)->after('tapu_tipi')
                ->comment('Konut kredisine uygun mu?');
            
            // Index (sık aranacak)
            $table->index('tapu_tipi', 'idx_ilanlar_tapu_tipi');
            $table->index('krediye_uygun', 'idx_ilanlar_krediye_uygun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_ilanlar_tapu_tipi');
            $table->dropIndex('idx_ilanlar_krediye_uygun');
            
            // Drop columns
            $table->dropColumn([
                'tapu_tipi',
                'krediye_uygun',
            ]);
        });
    }
};

