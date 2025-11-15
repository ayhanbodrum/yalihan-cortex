<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Context7: Rename musteri_tipi to kisi_tipi
     * This completes the musteri → kisi terminology standardization
     */
    public function up(): void
    {
        // Add kisi_tipi column to kisiler table (if not exists)
        if (!Schema::hasColumn('kisiler', 'kisi_tipi')) {
            Schema::table('kisiler', function (Blueprint $table) {
                $table->string('kisi_tipi', 50)->nullable()->after('status');
            });
            echo "✅ Added kisi_tipi column to kisiler table\n";
        }
        
        // Migrate data from musteri_tipi to kisi_tipi
        if (Schema::hasColumn('kisiler', 'musteri_tipi')) {
            DB::statement("
                UPDATE kisiler 
                SET kisi_tipi = COALESCE(kisi_tipi, musteri_tipi)
                WHERE musteri_tipi IS NOT NULL
            ");
            echo "✅ Migrated data: musteri_tipi → kisi_tipi\n";
            
            // Keep musteri_tipi for backward compatibility (don't drop yet)
            // Drop later after all code updated
            echo "⚠️ musteri_tipi column kept for backward compatibility\n";
            echo "⚠️ Will be dropped in future migration after full codebase update\n";
        }
        
        // Add index on kisi_tipi for better performance
        Schema::table('kisiler', function (Blueprint $table) {
            if (!$this->hasIndex('kisiler', 'kisiler_kisi_tipi_index')) {
                $table->index('kisi_tipi', 'kisiler_kisi_tipi_index');
                echo "✅ Added index on kisi_tipi\n";
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove kisi_tipi column
        if (Schema::hasColumn('kisiler', 'kisi_tipi')) {
            // Migrate data back to musteri_tipi before dropping
            DB::statement("
                UPDATE kisiler 
                SET musteri_tipi = COALESCE(musteri_tipi, kisi_tipi)
                WHERE kisi_tipi IS NOT NULL
            ");
            
            Schema::table('kisiler', function (Blueprint $table) {
                $table->dropIndex('kisiler_kisi_tipi_index');
                $table->dropColumn('kisi_tipi');
            });
        }
    }
    
    /**
     * Check if index exists
     */
    private function hasIndex($table, $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $index) {
            if ($index->Key_name === $indexName) {
                return true;
            }
        }
        return false;
    }
};

