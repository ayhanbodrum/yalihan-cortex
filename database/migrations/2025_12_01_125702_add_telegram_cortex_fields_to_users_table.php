<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Context7 Compliance Migration Template - Update Version
 *
 * ⚠️ CONTEXT7 PERMANENT STANDARDS:
 * - ALWAYS use 'display_order' field, NEVER use 'order'
 * - ALWAYS use 'status' field, NEVER use 'enabled', 'aktif', 'is_active'
 * - ALWAYS use DB::statement() for column renames (MySQL compatibility)
 * - ALWAYS preserve column properties (type, nullable, default)
 * - ALWAYS handle indexes before column rename
 *
 * @see .context7/MIGRATION_STANDARDS.md for complete migration standards
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ✅ CONTEXT7: Tablo varlık kontrolü
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            // Telegram Cortex Architecture - Context7 Standard: C7-TELEGRAM-CORTEX-2025-12-01
            if (!Schema::hasColumn('users', 'telegram_id')) {
                $table->string('telegram_id')->nullable()->unique()->after('telegram_chat_id')
                    ->comment('Telegram Chat ID (Cortex Architecture)');
            }
            if (!Schema::hasColumn('users', 'telegram_pairing_code')) {
                $table->string('telegram_pairing_code', 6)->nullable()->after('telegram_id')
                    ->comment('6 haneli geçici eşleşme kodu');
            }
            if (!Schema::hasColumn('users', 'telegram_paired_at')) {
                $table->timestamp('telegram_paired_at')->nullable()->after('telegram_pairing_code')
                    ->comment('Telegram eşleşme tarihi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            // Telegram Cortex Architecture Rollback
            if (Schema::hasColumn('users', 'telegram_id')) {
                $table->dropColumn('telegram_id');
            }
            if (Schema::hasColumn('users', 'telegram_pairing_code')) {
                $table->dropColumn('telegram_pairing_code');
            }
            if (Schema::hasColumn('users', 'telegram_paired_at')) {
                $table->dropColumn('telegram_paired_at');
            }
        });
    }
};
