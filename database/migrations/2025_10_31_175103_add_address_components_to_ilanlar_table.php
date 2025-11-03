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
            // 📍 Address Components (Structured Address Data)
            $table->string('sokak', 255)->nullable()->after('adres')->comment('Sokak adı (Street name)');
            $table->string('cadde', 255)->nullable()->after('sokak')->comment('Cadde adı (Avenue name)');
            $table->string('bulvar', 255)->nullable()->after('cadde')->comment('Bulvar adı (Boulevard name)');
            $table->string('bina_no', 20)->nullable()->after('bulvar')->comment('Bina numarası (Building number)');
            $table->string('daire_no', 20)->nullable()->after('bina_no')->comment('Daire/Ofis numarası (Apartment/Office number)');
            $table->string('posta_kodu', 10)->nullable()->after('daire_no')->comment('Posta kodu (Postal code)');
            
            // 📏 Distance Data (Nearby Important Locations)
            $table->json('nearby_distances')->nullable()->after('posta_kodu')->comment('Yakındaki önemli noktalara mesafeler [{name: "Deniz", distance: 500, unit: "m"}]');
            
            // 🗺️ Property Boundary (GeoJSON Polygon)
            $table->json('boundary_geojson')->nullable()->after('nearby_distances')->comment('Mülk sınırları (Property boundary polygon - GeoJSON format)');
            $table->decimal('boundary_area', 12, 2)->nullable()->after('boundary_geojson')->comment('Sınır alanı (Boundary area in m²) - Auto calculated');
            
            // Index'ler (Search optimization)
            $table->index('posta_kodu', 'idx_ilanlar_posta_kodu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Index'i kaldır
            $table->dropIndex('idx_ilanlar_posta_kodu');
            
            // Kolonları kaldır
            $table->dropColumn([
                'sokak',
                'cadde',
                'bulvar',
                'bina_no',
                'daire_no',
                'posta_kodu',
                'nearby_distances',
                'boundary_geojson',
                'boundary_area',
            ]);
        });
    }
};
