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
     * Context7: enabled field is FORBIDDEN, only status is allowed
     * This migration removes enabled columns if they exist
     */
    public function up(): void
    {
        // Check and remove enabled from features table (if exists)
        if (Schema::hasColumn('features', 'enabled')) {
            // First, copy enabled values to status (if status doesn't exist)
            if (! Schema::hasColumn('features', 'status')) {
                Schema::table('features', function (Blueprint $table) {
                    // Context7: display_order kullan (order değil)
                    $afterColumn = Schema::hasColumn('features', 'display_order') ? 'display_order' : 'order';
                    $table->boolean('status')->default(true)->after($afterColumn);
                });

                // Copy enabled → status
                DB::statement('UPDATE features SET status = enabled');
            } else {
                // If both exist, sync them then drop enabled
                DB::statement('UPDATE features SET status = enabled WHERE enabled IS NOT NULL');
            }

            // Now drop enabled column
            Schema::table('features', function (Blueprint $table) {
                $table->dropColumn('enabled');
            });
        }

        // Check and remove enabled from feature_categories table (if exists)
        if (Schema::hasColumn('feature_categories', 'enabled')) {
            // First, copy enabled values to status (if status doesn't exist)
            if (! Schema::hasColumn('feature_categories', 'status')) {
                Schema::table('feature_categories', function (Blueprint $table) {
                    // Context7: display_order kullan (order değil)
                    $afterColumn = Schema::hasColumn('feature_categories', 'display_order') ? 'display_order' : 'order';
                    $table->boolean('status')->default(true)->after($afterColumn);
                });

                // Copy enabled → status
                DB::statement('UPDATE feature_categories SET status = enabled');
            } else {
                // If both exist, sync them then drop enabled
                DB::statement('UPDATE feature_categories SET status = enabled WHERE enabled IS NOT NULL');
            }

            // Now drop enabled column
            Schema::table('feature_categories', function (Blueprint $table) {
                $table->dropColumn('enabled');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * ⚠️ WARNING: Context7 Violation - enabled field is FORBIDDEN
     * Rollback is NOT recommended as it violates Context7 standards
     */
    public function down(): void
    {
        // ⚠️ Context7: enabled field is FORBIDDEN
        // Rollback would create Context7 violations
        // This rollback is disabled to maintain Context7 compliance

        // If rollback is absolutely necessary, use:
        // php artisan migrate:rollback --step=1 --force
        // Then manually restore if needed

        echo "⚠️ WARNING: Rollback disabled - enabled field is FORBIDDEN per Context7 standards\n";
        echo "⚠️ If rollback is required, use: php artisan migrate:rollback --step=1 --force\n";
    }
};
