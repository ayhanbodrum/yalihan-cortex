<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Context7 Compliance Migration Template
 *
 * ⚠️ CONTEXT7 PERMANENT STANDARDS:
 * - ALWAYS use 'display_order' field, NEVER use 'order'
 * - ALWAYS use 'status' field, NEVER use 'enabled', 'aktif', 'is_active'
 * - Pre-commit hook will BLOCK migrations with forbidden patterns
 * - This is a PERMANENT STANDARD - NO EXCEPTIONS
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('ai_land_plot_analyses')) {
            Schema::table('ai_land_plot_analyses', function (Blueprint $table) {
                if (! Schema::hasColumn('ai_land_plot_analyses', 'display_order')) {
                    $table->integer('display_order')->default(0)->comment('Sıralama (Context7: order → display_order)');
                }
                if (! Schema::hasColumn('ai_land_plot_analyses', 'status')) {
                    $table->tinyInteger('status')->default(1)->comment('0=inactive, 1=active (Context7 boolean - PERMANENT STANDARD)');
                }
                if (! Schema::hasColumn('ai_land_plot_analyses', 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create('ai_land_plot_analyses', function (Blueprint $table) {
                $table->id();
                $table->integer('display_order')->default(0)->comment('Sıralama (Context7: order → display_order)');
                $table->tinyInteger('status')->default(1)->comment('0=inactive, 1=active (Context7 boolean - PERMANENT STANDARD)');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ai_land_plot_analyses')) {
            Schema::table('ai_land_plot_analyses', function (Blueprint $table) {
                if (Schema::hasColumn('ai_land_plot_analyses', 'display_order')) {
                    $table->dropColumn('display_order');
                }
                if (Schema::hasColumn('ai_land_plot_analyses', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};
