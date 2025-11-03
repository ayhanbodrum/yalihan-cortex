<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7 Fix: Status field MUST be boolean (TINYINT(1))
     * Violation ID: VIO-2025-01-24-001
     */
    public function up(): void
    {
        // Step 1: Önce field'ı VARCHAR'a çevir (eğer TINYINT ise)
        try {
            DB::statement('ALTER TABLE ilan_kategorileri MODIFY COLUMN status VARCHAR(50) NULL');
        } catch (\Exception $e) {
            // Zaten VARCHAR ise hata verme, devam et
        }

        // Step 2: Normalize existing data
        DB::statement("UPDATE ilan_kategorileri SET status = '1' WHERE status IN ('Aktif', '1', 'aktif', 'active', 'Active')");
        DB::statement("UPDATE ilan_kategorileri SET status = '0' WHERE status IN ('Pasif', '0', 'pasif', 'inactive', 'Inactive')");
        DB::statement("UPDATE ilan_kategorileri SET status = '1' WHERE status NOT IN ('0', '1')");

        // Step 3: Change column type to TINYINT(1) - boolean
        DB::statement('ALTER TABLE ilan_kategorileri MODIFY COLUMN status TINYINT(1) NOT NULL DEFAULT 1');

        // Log fix
        Log::info('Context7 Fix Applied: ilan_kategorileri.status field converted to TINYINT(1)', [
            'violation_id' => 'VIO-2025-01-24-001',
            'table' => 'ilan_kategorileri',
            'field' => 'status',
            'old_type' => 'VARCHAR/mixed',
            'new_type' => 'TINYINT(1)',
            'fixed_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to VARCHAR if needed (not recommended)
        DB::statement('ALTER TABLE ilan_kategorileri MODIFY COLUMN status VARCHAR(50) DEFAULT "Aktif"');

        // Convert back to string values
        DB::statement("
            UPDATE ilan_kategorileri
            SET status = CASE
                WHEN status = 1 THEN 'Aktif'
                WHEN status = 0 THEN 'Pasif'
                ELSE 'Aktif'
            END
        ");
    }
};
