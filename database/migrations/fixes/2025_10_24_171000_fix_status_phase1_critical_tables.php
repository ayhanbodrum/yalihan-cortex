<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * STATUS FIELD FIX - PHASE 1: CRITICAL TABLES
 *
 * Context7 Compliance: Status fields MUST be TINYINT(1) boolean
 * Violation ID: VIO-2025-10-24-PHASE1-CRITICAL
 *
 * PHASE 1 STRATEGY: Fix most critical tables first
 * - ilanlar (CRITICAL - main listings table)
 * - kisiler (CRITICAL - CRM core table)
 * - projeler (CRITICAL - projects system)
 * - ozellikler (CRITICAL - features system)
 * - talepler (CRITICAL - requests system)
 *
 * BACKUP RECOMMENDED BEFORE RUNNING!
 *
 * Timeline: Week 1 (2025-10-24)
 * Risk Level: MEDIUM (critical tables but controlled rollout)
 */
return new class extends Migration
{
    /**
     * PHASE 1: Critical tables to fix
     */
    private array $phase1Tables = [
        'ilanlar' => [
            'priority' => 'CRITICAL',
            'reason' => 'Main listings table - most used in entire system',
            'estimated_rows' => 'Unknown',
        ],
        'kisiler' => [
            'priority' => 'CRITICAL',
            'reason' => 'CRM core table - all contacts/customers',
            'estimated_rows' => 'Unknown',
        ],
        'projeler' => [
            'priority' => 'CRITICAL',
            'reason' => 'Projects system - portfolio management',
            'estimated_rows' => 'Unknown',
        ],
        'ozellikler' => [
            'priority' => 'CRITICAL',
            'reason' => 'Features system - property characteristics',
            'estimated_rows' => 'Unknown',
        ],
        'talepler' => [
            'priority' => 'CRITICAL',
            'reason' => 'Requests system - customer inquiries',
            'estimated_rows' => 'Unknown',
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Log::info('🚀 STATUS FIELD FIX - PHASE 1 BAŞLADI', [
            'phase' => 1,
            'tables' => array_keys($this->phase1Tables),
            'total_tables' => count($this->phase1Tables),
            'timeline' => 'Week 1 (2025-10-24)',
        ]);

        // Laravel migrations already handle transactions
        // No need for manual DB::beginTransaction()

        foreach ($this->phase1Tables as $table => $info) {
            if (!Schema::hasTable($table)) {
                Log::warning("⚠️  Table {$table} not found, skipping");
                continue;
            }

            if (!Schema::hasColumn($table, 'status')) {
                Log::warning("⚠️  Table {$table} has no status column, skipping");
                continue;
            }

            $this->fixTableStatus($table, $info);
        }

        Log::info('✅ STATUS FIELD FIX - PHASE 1 TAMAMLANDI', [
            'phase' => 1,
            'fixed_tables' => count($this->phase1Tables),
            'next_phase' => 'PHASE 2 (Week 2) - blog, location tables',
        ]);
    }

    /**
     * Fix a table's status field
     */
    private function fixTableStatus(string $table, array $info): void
    {
        try {
            Log::info("🔧 Fixing {$table}.status ({$info['priority']})...");
            Log::info("   Reason: {$info['reason']}");

            // Step 1: Get current data statistics
            $totalRows = DB::table($table)->count();
            $statusValues = DB::table($table)
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get();

            Log::info("   Current data:", [
                'total_rows' => $totalRows,
                'status_distribution' => $statusValues->pluck('count', 'status')->toArray()
            ]);

            // Step 2: Convert to VARCHAR first (to handle ENUM)
            DB::statement("ALTER TABLE {$table} MODIFY COLUMN status VARCHAR(50) NULL");

            // Step 3: Normalize data
            // Active values → '1'
            $activeUpdated = DB::statement("
                UPDATE {$table}
                SET status = '1'
                WHERE status IN ('Aktif', 'aktif', 'active', 'Active', '1', 1)
            ");

            // Inactive values → '0'
            $inactiveUpdated = DB::statement("
                UPDATE {$table}
                SET status = '0'
                WHERE status IN ('Pasif', 'pasif', 'inactive', 'Inactive', '0', 0)
            ");

            // NULL or unknown → '1' (default active)
            $unknownUpdated = DB::statement("
                UPDATE {$table}
                SET status = '1'
                WHERE status IS NULL OR status NOT IN ('0', '1')
            ");

            // Step 4: Convert to TINYINT(1)
            DB::statement("ALTER TABLE {$table} MODIFY COLUMN status TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0=inactive, 1=active (Context7 boolean - PHASE 1)'");

            // Step 5: Verify fix
            $verifyCount = DB::table($table)->count();
            $activeCount = DB::table($table)->where('status', 1)->count();
            $inactiveCount = DB::table($table)->where('status', 0)->count();

            Log::info("  ✅ {$table} FIXED!", [
                'total_rows' => $verifyCount,
                'active' => $activeCount,
                'inactive' => $inactiveCount,
                'priority' => $info['priority'],
            ]);

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
        Log::info('⏮️  REVERTING PHASE 1 STATUS FIELD FIX');

        foreach (array_keys($this->phase1Tables) as $table) {
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

        Log::warning('⚠️  PHASE 1 ROLLBACK COMPLETED - Status fields reverted');
    }
};

