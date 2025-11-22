<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Context7 Compliance: Status field MUST be boolean (TINYINT(1))
 * 
 * Sorun: status kolonu varchar(255) ve 'Aktif' string değeri kullanıyor
 * Bu tutarsızlık sürekli sorun yaratıyor (true/false vs 'Aktif'/'Pasif')
 * 
 * Çözüm: status kolonunu TINYINT(1) boolean'a çevir
 * Böylece kodda true/false kullanabiliriz, veritabanında otomatik 1/0 olur
 * 
 * @see .context7/MIGRATION_STANDARDS.md
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ✅ CONTEXT7: Tablo varlık kontrolü
        if (!Schema::hasTable('ilan_kategorileri')) {
            return;
        }

        // ✅ CONTEXT7: Kolon varlık kontrolü
        if (!Schema::hasColumn('ilan_kategorileri', 'status')) {
            return;
        }

        try {
            Log::info('🔧 Converting ilan_kategorileri.status from VARCHAR to TINYINT(1)...');

            // Step 1: Önce field'ı VARCHAR'a çevir (eğer zaten VARCHAR değilse)
            // Bu adım güvenli, zaten VARCHAR ise hata vermez
            try {
                DB::statement('ALTER TABLE ilan_kategorileri MODIFY COLUMN status VARCHAR(50) NULL');
            } catch (\Exception $e) {
                // Zaten VARCHAR ise devam et
                Log::info('  ℹ️  Status column already VARCHAR, continuing...');
            }

            // Step 2: Mevcut verileri normalize et
            // 'Aktif', 'aktif', 'active', 'Active', '1', 1 → '1'
            DB::statement("
                UPDATE ilan_kategorileri
                SET status = '1'
                WHERE status IN ('Aktif', 'aktif', 'active', 'Active', '1', 1)
            ");

            // 'Pasif', 'pasif', 'inactive', 'Inactive', '0', 0 → '0'
            DB::statement("
                UPDATE ilan_kategorileri
                SET status = '0'
                WHERE status IN ('Pasif', 'pasif', 'inactive', 'Inactive', '0', 0)
            ");

            // NULL veya bilinmeyen değerler → '1' (default active)
            DB::statement("
                UPDATE ilan_kategorileri
                SET status = '1'
                WHERE status IS NULL OR status NOT IN ('0', '1')
            ");

            // Step 3: Kolon tipini TINYINT(1) boolean'a çevir
            DB::statement('ALTER TABLE ilan_kategorileri MODIFY COLUMN status TINYINT(1) NOT NULL DEFAULT 1 COMMENT \'0=inactive, 1=active (Context7 boolean)\'');

            $affectedRows = DB::table('ilan_kategorileri')->count();
            Log::info("  ✅ ilan_kategorileri.status converted successfully ({$affectedRows} rows)");

        } catch (\Exception $e) {
            Log::error('  ❌ ilan_kategorileri.status conversion failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('ilan_kategorileri')) {
            return;
        }

        if (!Schema::hasColumn('ilan_kategorileri', 'status')) {
            return;
        }

        try {
            Log::info('⏮️  Reverting ilan_kategorileri.status to VARCHAR...');

            // Step 1: TINYINT'ten VARCHAR'a çevir
            DB::statement('ALTER TABLE ilan_kategorileri MODIFY COLUMN status VARCHAR(50) NULL');

            // Step 2: Verileri string'e çevir
            DB::statement("UPDATE ilan_kategorileri SET status = 'Aktif' WHERE status = 1");
            DB::statement("UPDATE ilan_kategorileri SET status = 'Pasif' WHERE status = 0");

            // Step 3: Default değeri 'Aktif' yap
            DB::statement("ALTER TABLE ilan_kategorileri MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Aktif'");

            Log::info('  ✅ Reverted successfully');

        } catch (\Exception $e) {
            Log::error('  ❌ Revert failed: ' . $e->getMessage());
            throw $e;
        }
    }
};
