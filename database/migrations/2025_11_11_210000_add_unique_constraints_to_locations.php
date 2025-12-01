<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Context7: Duplicate önleme için unique constraint ekle
     */
    public function up(): void
    {
        // İlçeler için unique constraint (il_id + ilce_adi)
        Schema::table('ilceler', function (Blueprint $table) {
            // Önce mevcut duplicate'leri kontrol et
            // Unique constraint ekle
            $table->unique(['il_id', 'ilce_adi'], 'ilceler_il_id_ilce_adi_unique');
        });

        // Mahalleler için unique constraint (ilce_id + mahalle_adi)
        Schema::table('mahalleler', function (Blueprint $table) {
            // Unique constraint ekle
            $table->unique(['ilce_id', 'mahalle_adi'], 'mahalleler_ilce_id_mahalle_adi_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilceler', function (Blueprint $table) {
            $table->dropUnique('ilceler_il_id_ilce_adi_unique');
        });

        Schema::table('mahalleler', function (Blueprint $table) {
            $table->dropUnique('mahalleler_ilce_id_mahalle_adi_unique');
        });
    }
};
