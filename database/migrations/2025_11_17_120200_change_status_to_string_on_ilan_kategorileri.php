<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ilan_kategorileri') && Schema::hasColumn('ilan_kategorileri', 'status')) {
            Schema::table('ilan_kategorileri', function (Blueprint $table) {
                $table->string('status')->default('Aktif')->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ilan_kategorileri') && Schema::hasColumn('ilan_kategorileri', 'status')) {
            Schema::table('ilan_kategorileri', function (Blueprint $table) {
                $table->boolean('status')->default(true)->change();
            });
        }
    }
};
