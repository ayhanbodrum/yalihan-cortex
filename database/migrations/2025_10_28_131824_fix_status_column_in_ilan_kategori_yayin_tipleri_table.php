<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ✅ Context7: Status column VARCHAR "Aktif"/"Pasif" → TINYINT(1) 1/0
        
        // Step 1: Update existing data
        DB::table('ilan_kategori_yayin_tipleri')->update([
            'status' => DB::raw("CASE 
                WHEN status = 'Aktif' THEN 1
                WHEN status = 'Pasif' THEN 0
                WHEN status = '1' THEN 1
                WHEN status = '0' THEN 0
                WHEN status = 1 THEN 1
                WHEN status = 0 THEN 0
                ELSE 1
            END")
        ]);
        
        // Step 2: Change column type to TINYINT(1)
        Schema::table('ilan_kategori_yayin_tipleri', function (Blueprint $table) {
            $table->boolean('status')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: TINYINT(1) → VARCHAR
        Schema::table('ilan_kategori_yayin_tipleri', function (Blueprint $table) {
            $table->string('status', 20)->default('Aktif')->change();
        });
        
        // Restore string values
        DB::table('ilan_kategori_yayin_tipleri')->update([
            'status' => DB::raw("CASE 
                WHEN status = 1 THEN 'Aktif'
                WHEN status = 0 THEN 'Pasif'
                ELSE 'Aktif'
            END")
        ]);
    }
};
