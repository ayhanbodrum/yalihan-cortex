<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Context7 Compliance: enabled → status migration
     *
     * Tüm tablolardaki 'enabled' kolonlarını 'status' olarak yeniden adlandır
     */
    public function up(): void
    {
        $tables = [
            'alt_kategori_yayin_tipi',
            'kategori_yayin_tipi_field_dependencies',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'enabled') && ! Schema::hasColumn($table, 'status')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->renameColumn('enabled', 'status');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'alt_kategori_yayin_tipi',
            'kategori_yayin_tipi_field_dependencies',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'status') && ! Schema::hasColumn($table, 'enabled')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->renameColumn('status', 'enabled');
                });
            }
        }
    }
};
