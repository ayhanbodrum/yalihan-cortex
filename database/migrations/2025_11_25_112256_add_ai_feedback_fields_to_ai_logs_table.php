<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
     *
     * Context7 Standardı: C7-AI-FEEDBACK-2025-11-25
     * AI öğrenme döngüsü için danışman geri bildirim alanları ekleniyor
     */
    public function up(): void
    {
        // ✅ CONTEXT7: Tablo varlık kontrolü
        if (! Schema::hasTable('ai_logs')) {
            return;
        }

        Schema::table('ai_logs', function (Blueprint $table) {
            // User rating: 1-5 arası AI skorunun doğruluğu
            if (! Schema::hasColumn('ai_logs', 'user_rating')) {
                $table->tinyInteger('user_rating')->nullable()->after('ip_address')
                    ->comment('Kullanıcı değerlendirmesi (1-5 arası, AI skorunun doğruluğu)');
            }

            // Feedback type: positive, negative, neutral
            if (! Schema::hasColumn('ai_logs', 'feedback_type')) {
                $table->string('feedback_type', 20)->nullable()->after('user_rating')
                    ->comment('Geri bildirim tipi: positive, negative, neutral');
            }

            // Feedback reason: danışman yorumu/nedeni
            if (! Schema::hasColumn('ai_logs', 'feedback_reason')) {
                $table->text('feedback_reason')->nullable()->after('feedback_type')
                    ->comment('Geri bildirim nedeni/yorumu (danışman açıklaması)');
            }

            // Feedback timestamp
            if (! Schema::hasColumn('ai_logs', 'feedback_at')) {
                $table->timestamp('feedback_at')->nullable()->after('feedback_reason')
                    ->comment('Geri bildirimin verildiği zaman');
            }
        });

        // Index ekle (performans için)
        if (Schema::hasTable('ai_logs') && ! Schema::hasColumn('ai_logs', 'feedback_type')) {
            // Index'ler migration içinde otomatik eklenir
        } else {
            // Feedback type için index ekle (sorgu performansı için)
            try {
                DB::statement('CREATE INDEX ai_logs_feedback_type_index ON ai_logs(feedback_type)');
            } catch (\Exception $e) {
                // Index zaten varsa hata verme
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('ai_logs')) {
            return;
        }

        Schema::table('ai_logs', function (Blueprint $table) {
            // ✅ CONTEXT7: Rollback işlemleri - Feedback alanlarını kaldır
            if (Schema::hasColumn('ai_logs', 'feedback_at')) {
                $table->dropColumn('feedback_at');
            }
            if (Schema::hasColumn('ai_logs', 'feedback_reason')) {
                $table->dropColumn('feedback_reason');
            }
            if (Schema::hasColumn('ai_logs', 'feedback_type')) {
                $table->dropColumn('feedback_type');
            }
            if (Schema::hasColumn('ai_logs', 'user_rating')) {
                $table->dropColumn('user_rating');
            }
        });

        // Index'i kaldır
        try {
            DB::statement('DROP INDEX ai_logs_feedback_type_index ON ai_logs');
        } catch (\Exception $e) {
            // Index yoksa hata verme
        }
    }
};
