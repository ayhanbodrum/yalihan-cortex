<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7: Adding mahalle_id to kisiler table for consistency
     * Date: 2025-10-31
     * Reason: Location system standardization - all address systems should support mahalle
     */
    public function up(): void
    {
        Schema::table('kisiler', function (Blueprint $table) {
            // Add mahalle_id column after ilce_id
            $table->unsignedBigInteger('mahalle_id')->nullable()->after('ilce_id');

            // Add foreign key constraint
            $table->foreign('mahalle_id')
                ->references('id')
                ->on('mahalleler')
                ->onDelete('set null');

            // Add index for location queries
            $table->index('mahalle_id', 'idx_kisiler_mahalle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kisiler', function (Blueprint $table) {
            // Drop foreign key and index first
            $table->dropForeign(['mahalle_id']);
            $table->dropIndex('idx_kisiler_mahalle');

            // Drop column
            $table->dropColumn('mahalle_id');
        });
    }
};
