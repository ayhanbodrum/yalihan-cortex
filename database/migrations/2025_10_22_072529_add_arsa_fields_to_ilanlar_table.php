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
            // Arsa özellikleri (Land Properties)
            $table->string('ada_no', 50)->nullable()->after('adres')->comment('Ada numarası');
            $table->string('parsel_no', 50)->nullable()->after('ada_no')->comment('Parsel numarası');
            $table->string('ada_parsel', 100)->nullable()->after('parsel_no')->comment('Ada/Parsel birleşik (legacy)');

            // İmar durumu (Zoning Status)
            $table->string('imar_statusu', 100)->nullable()->after('ada_parsel')->comment('İmar durumu: İmarlı, İmarsız, Tarla');

            // Yapılaşma katsayıları (Construction Coefficients)
            $table->decimal('kaks', 5, 2)->nullable()->after('imar_statusu')->comment('Kat Alanı Kat Sayısı (Floor Area Ratio)');
            $table->decimal('taks', 5, 2)->nullable()->after('kaks')->comment('Taban Alanı Kat Sayısı (Building Coverage Ratio)');
            $table->decimal('gabari', 5, 2)->nullable()->after('taks')->comment('Gabari (maksimum bina yüksekliği)');
            $table->decimal('alan_m2', 12, 2)->nullable()->after('gabari')->comment('Arsa alanı (m²)');
            $table->decimal('taban_alani', 12, 2)->nullable()->after('alan_m2')->comment('Taban alanı (m²)');

            // Altyapı (Infrastructure) - Boolean
            $table->boolean('yola_cephe')->default(false)->after('taban_alani')->comment('Yola cephesi var mı?');
            $table->decimal('yola_cephesi', 8, 2)->nullable()->after('yola_cephe')->comment('Yola cephe mesafesi (m) - legacy');
            $table->boolean('altyapi_elektrik')->default(false)->after('yola_cephesi')->comment('Elektrik altyapısı');
            $table->boolean('altyapi_su')->default(false)->after('altyapi_elektrik')->comment('Su altyapısı');
            $table->boolean('altyapi_dogalgaz')->default(false)->after('altyapi_su')->comment('Doğalgaz altyapısı');

            // Legacy altyapı field'ları (backward compatibility)
            $table->boolean('elektrik_altyapisi')->default(false)->after('altyapi_dogalgaz')->comment('Legacy: Elektrik');
            $table->boolean('su_altyapisi')->default(false)->after('elektrik_altyapisi')->comment('Legacy: Su');
            $table->boolean('dogalgaz_altyapisi')->default(false)->after('su_altyapisi')->comment('Legacy: Doğalgaz');

            // Index'ler
            $table->index(['ada_no', 'parsel_no'], 'idx_ilanlar_ada_parsel');
            $table->index('imar_statusu', 'idx_ilanlar_imar_statusu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Index'leri kaldır
            $table->dropIndex('idx_ilanlar_ada_parsel');
            $table->dropIndex('idx_ilanlar_imar_statusu');

            // Kolonları kaldır
            $table->dropColumn([
                'ada_no', 'parsel_no', 'ada_parsel', 'imar_statusu',
                'kaks', 'taks', 'gabari', 'alan_m2', 'taban_alani',
                'yola_cephe', 'yola_cephesi',
                'altyapi_elektrik', 'altyapi_su', 'altyapi_dogalgaz',
                'elektrik_altyapisi', 'su_altyapisi', 'dogalgaz_altyapisi',
            ]);
        });
    }
};
