<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * MASTER STATUS FIELD FIX - 22 TABLES
 *
 * Context7 Compliance: Status fields MUST be TINYINT(1) boolean
 * Violation ID: VIO-2025-10-24-MASTER-STATUS-CRISIS
 *
 * This migration fixes ALL inconsistent status fields across the database.
 *
 * AFFECTED TABLES: 22
 * - 15 tables with VARCHAR + Turkish 'Aktif'
 * - 6 tables with ENUM + Turkish 'Aktif'/'Pasif'
 * - 1 table with complex ENUM (yazlik_rezervasyonlar - SKIP)
 *
 * BACKUP RECOMMENDED BEFORE RUNNING!
 */
return new class extends Migration
{
    /**
     * Tables to fix (simple boolean status)
     */
    private array $simpleBooleanTables = [
        'ai_logs',
        'blog_categories',
        'blog_tags',
        'ilanlar',
        'ilan_ozellikleri',
        'ilan_resimleri',
        'ilceler',
        'iller',
        'kisiler',
        'mahalleler',
        'ozellik_kategorileri',
        'ozellikler',
        'projeler',
        'takim_uyeleri',
        'talepler',
        'ulkeler',
    ];

    /**
     * Tables with complex status (keep as VARCHAR or ENUM)
     * These need manual review - status is not simple active/inactive
     */
    private array $complexStatusTables = [
        'blog_comments',     // Beklemede, Onaylandi, Spam, etc.
        'blog_posts',        // draft, published, scheduled, etc.
        'eslesmeler',        // beklemede, eslesti, iptal, etc.
        'gorevler',          // Beklemede, Devam Ediyor, Tamamlandi, etc.
        'sites',             // active, inactive, pending
        'yazlik_rezervasyonlar', // beklemede, onaylandi, iptal, tamamlandi
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Log::info('🚀 MASTER STATUS FIELD FIX BAŞLADI', [
            'simple_tables' => count($this->simpleBooleanTables),
            'complex_tables' => count($this->complexStatusTables),
            'total_affected' => count($this->simpleBooleanTables) + count($this->complexStatusTables),
        ]);

        DB::beginTransaction();

        try {
            // PHASE 1: Fix simple boolean status fields
            foreach ($this->simpleBooleanTables as $table) {
                if (!Schema::hasTable($table)) {
                    Log::warning("⚠️  Table {$table} not found, skipping");
                    continue;
                }

                if (!Schema::hasColumn($table, 'status')) {
                    Log::warning("⚠️  Table {$table} has no status column, skipping");
                    continue;
                }

                $this->fixSimpleBooleanStatus($table);
            }

            // PHASE 2: Log complex status tables (manual review needed)
            Log::info('📝 Complex status tables (manual review needed):', [
                'tables' => $this->complexStatusTables,
                'note' => 'These tables have multi-state status, not simple boolean. Review individually.'
            ]);

            DB::commit();

            Log::info('✅ MASTER STATUS FIELD FIX TAMAMLANDI', [
                'fixed_tables' => count($this->simpleBooleanTables),
                'review_needed' => count($this->complexStatusTables),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ MASTER STATUS FIELD FIX FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Fix a simple boolean status field
     */
    private function fixSimpleBooleanStatus(string $table): void
    {
        try {
            Log::info("🔧 Fixing {$table}.status...");

            // Step 1: Convert to VARCHAR first (to handle ENUM)
            DB::statement("ALTER TABLE {$table} MODIFY COLUMN status VARCHAR(50) NULL");

            // Step 2: Normalize data
            // Active values → '1'
            DB::statement("
                UPDATE {$table}
                SET status = '1'
                WHERE status IN ('Aktif', 'aktif', 'active', 'Active', '1', 1)
            ");

            // Inactive values → '0'
            DB::statement("
                UPDATE {$table}
                SET status = '0'
                WHERE status IN ('Pasif', 'pasif', 'inactive', 'Inactive', '0', 0)
            ");

            // NULL or unknown → '1' (default active)
            DB::statement("
                UPDATE {$table}
                SET status = '1'
                WHERE status IS NULL OR status NOT IN ('0', '1')
            ");

            // Step 3: Convert to TINYINT(1)
            DB::statement("ALTER TABLE {$table} MODIFY COLUMN status TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0=inactive, 1=active (Context7 boolean)'");

            $affectedRows = DB::table($table)->count();
            Log::info("  ✅ {$table} fixed successfully ({$affectedRows} rows)");

        } catch (\Exception $e) {
            Log::error("  ❌ {$table} fix failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Log::info('⏮️  REVERTING MASTER STATUS FIELD FIX');

        // Revert simple boolean tables to VARCHAR
        foreach ($this->simpleBooleanTables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            try {
                // Convert back to VARCHAR
                DB::statement("ALTER TABLE {$table} MODIFY COLUMN status VARCHAR(255) DEFAULT 'Aktif'");

                // Convert values back to Turkish strings
                DB::statement("
                    UPDATE {$table}
                    SET status = CASE
                        WHEN status = 1 THEN 'Aktif'
                        WHEN status = 0 THEN 'Pasif'
                        ELSE 'Aktif'
                    END
                ");

                Log::info("  ✅ {$table} reverted to VARCHAR");

            } catch (\Exception $e) {
                Log::error("  ❌ {$table} revert failed: " . $e->getMessage());
            }
        }

        Log::warning('⚠️  ROLLBACK COMPLETED - Status fields reverted to old format');
    }
};
