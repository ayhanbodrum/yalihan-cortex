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
        Schema::table('mahalleler', function (Blueprint $table) {
            // Koordinat bilgileri ekle
            $table->decimal('enlem', 10, 8)->nullable()->after('mahalle_kodu')->comment('Latitude coordinate');
            $table->decimal('boylam', 11, 8)->nullable()->after('enlem')->comment('Longitude coordinate');

            // Status alanı ekle
            $table->enum('status', ['Aktif', 'Pasif'])->default('Aktif')->after('boylam')->comment('Mahalle status');

            // Index'ler ekle
            $table->index(['enlem', 'boylam'], 'idx_mahalleler_coordinates');
            $table->index('status', 'idx_mahalleler_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahalleler', function (Blueprint $table) {
            $table->dropIndex('idx_mahalleler_coordinates');
            $table->dropIndex('idx_mahalleler_status');
            $table->dropColumn(['enlem', 'boylam', 'status']);
        });
    }
};
