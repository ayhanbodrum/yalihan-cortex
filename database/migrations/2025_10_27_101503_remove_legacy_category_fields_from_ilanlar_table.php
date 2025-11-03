<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            $connection = DB::connection();

            if ($connection->getDriverName() === 'mysql') {
                $foreignKeys = $connection->select("
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'ilanlar'
                    AND COLUMN_NAME IN ('kategori_id', 'parent_kategori_id')
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");

                foreach ($foreignKeys as $fk) {
                    $table->dropForeign([$fk->CONSTRAINT_NAME]);
                }
            }

            if (Schema::hasColumn('ilanlar', 'kategori_id')) {
                $table->dropColumn('kategori_id');
            }

            if (Schema::hasColumn('ilanlar', 'parent_kategori_id')) {
                $table->dropColumn('parent_kategori_id');
            }

            if (Schema::hasColumn('ilanlar', 'yayinlama_tipi')) {
                $table->dropColumn('yayinlama_tipi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            if (!Schema::hasColumn('ilanlar', 'kategori_id')) {
                $table->foreignId('kategori_id')->nullable()->constrained('ilan_kategorileri')->onDelete('set null');
            }

            if (!Schema::hasColumn('ilanlar', 'parent_kategori_id')) {
                $table->foreignId('parent_kategori_id')->nullable()->constrained('ilan_kategorileri')->onDelete('set null');
            }

            if (!Schema::hasColumn('ilanlar', 'yayinlama_tipi')) {
                $table->string('yayinlama_tipi')->nullable();
            }
        });
    }
};
