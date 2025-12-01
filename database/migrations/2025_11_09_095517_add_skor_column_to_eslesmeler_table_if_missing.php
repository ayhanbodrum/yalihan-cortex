<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7 Compliance: Add 'skor' column to eslesmeler table if it doesn't exist
     */
    public function up(): void
    {
        if (! Schema::hasTable('eslesmeler')) {
            return;
        }

        if (! Schema::hasColumn('eslesmeler', 'skor')) {
            Schema::table('eslesmeler', function (Blueprint $table) {
                $table->integer('skor')->default(0)->after('danisman_id');
            });

            // Add index for skor column
            Schema::table('eslesmeler', function (Blueprint $table) {
                $table->index('skor');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('eslesmeler', 'skor')) {
            Schema::table('eslesmeler', function (Blueprint $table) {
                $table->dropIndex(['skor']);
                $table->dropColumn('skor');
            });
        }
    }
};
