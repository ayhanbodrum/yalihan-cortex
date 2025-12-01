<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7: Remove all 'enabled' fields and ensure ONLY 'status' is used
     * This completes the enabled → status standardization
     */
    public function up(): void
    {
        // Note: Features and feature_categories already use 'status' column
        // This migration ensures NO 'enabled' column exists anywhere

        // Check and remove 'enabled' from features table (if exists)
        if (Schema::hasColumn('features', 'enabled')) {
            // If data migration needed (shouldn't be, but safety first)
            DB::statement('
                UPDATE features 
                SET status = COALESCE(enabled, status, 0) 
                WHERE enabled IS NOT NULL
            ');

            Schema::table('features', function (Blueprint $table) {
                $table->dropColumn('enabled');
            });

            echo "✅ Removed 'enabled' column from features table\n";
        }

        // Check and remove 'enabled' from feature_categories table (if exists)
        if (Schema::hasColumn('feature_categories', 'enabled')) {
            // If data migration needed
            DB::statement('
                UPDATE feature_categories 
                SET status = COALESCE(enabled, status, 0) 
                WHERE enabled IS NOT NULL
            ');

            Schema::table('feature_categories', function (Blueprint $table) {
                $table->dropColumn('enabled');
            });

            echo "✅ Removed 'enabled' column from feature_categories table\n";
        }

        // Verify status column exists in both tables
        if (! Schema::hasColumn('features', 'status')) {
            Schema::table('features', function (Blueprint $table) {
                // Context7: display_order kullan (order değil)
                $afterColumn = Schema::hasColumn('features', 'display_order') ? 'display_order' : 'order';
                $table->boolean('status')->default(true)->after($afterColumn);
            });
            echo "✅ Added 'status' column to features table\n";
        }

        if (! Schema::hasColumn('feature_categories', 'status')) {
            Schema::table('feature_categories', function (Blueprint $table) {
                // Context7: display_order kullan (order değil)
                $afterColumn = Schema::hasColumn('feature_categories', 'display_order') ? 'display_order' : 'order';
                $table->boolean('status')->default(true)->after($afterColumn);
            });
            echo "✅ Added 'status' column to feature_categories table\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback not recommended (Context7 violation)
        // If needed, manually restore 'enabled' columns
        echo "⚠️ WARNING: Rollback would create Context7 violations\n";
        echo "⚠️ Migration rollback is NOT recommended\n";
    }
};
