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
     * 
     * Context7 Standard: C7-ENVIRONMENT-FIELDS-2025-12-04
     * Adds environment-related fields for POI and tags storage
     */
    public function up(): void
    {
        if (!Schema::hasTable('ilanlar')) {
            return;
        }

        Schema::table('ilanlar', function (Blueprint $table) {
            // Points of Interest (POI) - Seçili yakın lokasyonlar
            if (!Schema::hasColumn('ilanlar', 'environment_pois')) {
                $table->json('environment_pois')->nullable()->after('lng')
                    ->comment('Yakın lokasyonlar (POI) - JSON array: [{id, name, type, category, lat, lng, distance_m, walking_minutes}]');
            }

            // Çevresel etiketler (sakin, aile, turistik, yatırımcı dostu, vb.)
            if (!Schema::hasColumn('ilanlar', 'environment_tags')) {
                $table->json('environment_tags')->nullable()->after('environment_pois')
                    ->comment('Çevresel etiketler - JSON array: ["sakin", "aile", "turistik", "yatirimci_dostu"]');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('ilanlar')) {
            return;
        }

        Schema::table('ilanlar', function (Blueprint $table) {
            if (Schema::hasColumn('ilanlar', 'environment_tags')) {
                $table->dropColumn('environment_tags');
            }
            if (Schema::hasColumn('ilanlar', 'environment_pois')) {
                $table->dropColumn('environment_pois');
            }
        });
    }
};
