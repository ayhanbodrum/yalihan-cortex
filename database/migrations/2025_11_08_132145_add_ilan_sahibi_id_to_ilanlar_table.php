<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Check if columns already exist
            if (! Schema::hasColumn('ilanlar', 'ilan_sahibi_id')) {
                $table->unsignedBigInteger('ilan_sahibi_id')->nullable()->after('danisman_id')->comment('İlan sahibi (Kisi ID)');
            }
            if (! Schema::hasColumn('ilanlar', 'ilgili_kisi_id')) {
                $table->unsignedBigInteger('ilgili_kisi_id')->nullable()->after('ilan_sahibi_id')->comment('İlgili kişi (Kisi ID)');
            }
        });

        // Add foreign keys separately to avoid errors if they already exist
        Schema::table('ilanlar', function (Blueprint $table) {
            if (Schema::hasColumn('ilanlar', 'ilan_sahibi_id')) {
                // Check if foreign key exists before dropping
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'ilanlar' 
                    AND COLUMN_NAME = 'ilan_sahibi_id' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");

                if (! empty($foreignKeys)) {
                    try {
                        $table->dropForeign(['ilan_sahibi_id']);
                    } catch (\Exception $e) {
                        // Foreign key doesn't exist, continue
                    }
                }

                try {
                    $table->foreign('ilan_sahibi_id')->references('id')->on('kisiler')->onDelete('set null');
                } catch (\Exception $e) {
                    // Foreign key already exists, continue
                }

                // Add index if not exists
                $indexes = DB::select("SHOW INDEXES FROM ilanlar WHERE Column_name = 'ilan_sahibi_id'");
                if (empty($indexes)) {
                    $table->index('ilan_sahibi_id');
                }
            }

            if (Schema::hasColumn('ilanlar', 'ilgili_kisi_id')) {
                // Check if foreign key exists before dropping
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'ilanlar' 
                    AND COLUMN_NAME = 'ilgili_kisi_id' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");

                if (! empty($foreignKeys)) {
                    try {
                        $table->dropForeign(['ilgili_kisi_id']);
                    } catch (\Exception $e) {
                        // Foreign key doesn't exist, continue
                    }
                }

                try {
                    $table->foreign('ilgili_kisi_id')->references('id')->on('kisiler')->onDelete('set null');
                } catch (\Exception $e) {
                    // Foreign key already exists, continue
                }

                // Add index if not exists
                $indexes = DB::select("SHOW INDEXES FROM ilanlar WHERE Column_name = 'ilgili_kisi_id'");
                if (empty($indexes)) {
                    $table->index('ilgili_kisi_id');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            $table->dropForeign(['ilan_sahibi_id']);
            $table->dropForeign(['ilgili_kisi_id']);
            $table->dropIndex(['ilan_sahibi_id']);
            $table->dropIndex(['ilgili_kisi_id']);
            $table->dropColumn(['ilan_sahibi_id', 'ilgili_kisi_id']);
        });
    }
};
