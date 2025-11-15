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
        Schema::table('site_apartmanlar', function (Blueprint $table) {
            // Koordinat bilgileri (WikiMapia entegrasyonu için)
            $table->decimal('latitude', 10, 6)->nullable()->after('mahalle_id')->comment('Enlem (WikiMapia)');
            $table->decimal('longitude', 10, 6)->nullable()->after('latitude')->comment('Boylam (WikiMapia)');

            // Index ekle (hızlı arama için)
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_apartmanlar', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
