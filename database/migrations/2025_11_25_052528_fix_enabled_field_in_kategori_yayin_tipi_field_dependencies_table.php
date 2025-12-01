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
     * Context7 Compliance: enabled → status migration
     */
    public function up(): void
    {
        // ✅ CONTEXT7: Tablo varlık kontrolü
        if (! Schema::hasTable('kategori_yayin_tipi_field_dependencies')) {
            return;
        }

        // Context7 Compliance: enabled → status
        if (Schema::hasColumn('kategori_yayin_tipi_field_dependencies', 'enabled')) {
            Schema::table('kategori_yayin_tipi_field_dependencies', function (Blueprint $table) {
                // Yeni status kolonu ekle
                $table->string('status', 20)->default('Aktif')->after('field_unit');
            });

            // Veriyi migrate et: enabled = true → status = 'Aktif', enabled = false → status = 'Pasif'
            DB::statement("
                UPDATE kategori_yayin_tipi_field_dependencies
                SET status = CASE
                    WHEN enabled = 1 THEN 'Aktif'
                    ELSE 'Pasif'
                END
            ");

            Schema::table('kategori_yayin_tipi_field_dependencies', function (Blueprint $table) {
                // Eski enabled kolonu ve index'ini sil
                $table->dropIndex('idx_enabled');
                $table->dropColumn('enabled');

                // Yeni status index'i ekle
                $table->index('status', 'idx_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('kategori_yayin_tipi_field_dependencies')) {
            return;
        }

        if (Schema::hasColumn('kategori_yayin_tipi_field_dependencies', 'status')) {
            Schema::table('kategori_yayin_tipi_field_dependencies', function (Blueprint $table) {
                // Boolean field geri ekle (rollback için)
                $table->boolean('old_field')->default(true)->after('field_unit');
            });

            // Veriyi geri migrate et
            DB::statement("
                UPDATE kategori_yayin_tipi_field_dependencies
                SET old_field = CASE
                    WHEN status = 'Aktif' THEN 1
                    ELSE 0
                END
            ");

            Schema::table('kategori_yayin_tipi_field_dependencies', function (Blueprint $table) {
                // status kolonu ve index'ini sil
                $table->dropIndex('idx_status');
                $table->dropColumn('status');

                // Eski field için index ekle
                $table->index('old_field', 'idx_old_field');
            });
        }
    }
};
