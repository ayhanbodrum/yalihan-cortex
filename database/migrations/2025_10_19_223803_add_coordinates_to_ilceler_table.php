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
        Schema::table('ilceler', function (Blueprint $table) {
            // Status alanı ekle (koordinatlar zaten var: lat, lng)
            $table->enum('status', ['Aktif', 'Pasif'])->default('Aktif')->after('lng')->comment('District status');
            
            // Index'ler ekle
            $table->index(['lat', 'lng'], 'idx_ilceler_coordinates');
            $table->index('status', 'idx_ilceler_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilceler', function (Blueprint $table) {
            $table->dropIndex('idx_ilceler_coordinates');
            $table->dropIndex('idx_ilceler_status');
            $table->dropColumn('status');
        });
    }
};