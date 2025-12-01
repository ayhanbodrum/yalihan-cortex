<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Context7: durum → status migration
     */
    public function up(): void
    {
        Schema::table('takim_uyeleri', function (Blueprint $table) {
            // Status kolonu yoksa ekle
            if (! Schema::hasColumn('takim_uyeleri', 'status')) {
                $table->string('status', 50)->default('aktif')->after('durum');
            }
        });

        // Veri taşıma: durum → status (ENUM değerlerini string'e çevir)
        if (Schema::hasColumn('takim_uyeleri', 'durum') && Schema::hasColumn('takim_uyeleri', 'status')) {
            DB::statement("UPDATE takim_uyeleri SET status = durum WHERE status IS NULL OR status = ''");

            // Eğer durum kolonu varsa ve status kolonu doluysa, durum kolonunu kaldır
            // (Önce tüm kodların status kullanmasını sağlamalıyız)
        }

        // Index ekle
        Schema::table('takim_uyeleri', function (Blueprint $table) {
            if (Schema::hasColumn('takim_uyeleri', 'status')) {
                if (! $this->indexExists('takim_uyeleri', 'idx_takim_uyeleri_status')) {
                    $table->index('status', 'idx_takim_uyeleri_status');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('takim_uyeleri', function (Blueprint $table) {
            if (Schema::hasColumn('takim_uyeleri', 'status')) {
                $table->dropIndex('idx_takim_uyeleri_status');
                $table->dropColumn('status');
            }
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();

        $result = DB::select(
            'SELECT COUNT(*) as count FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$databaseName, $table, $index]
        );

        return $result[0]->count > 0;
    }
};
