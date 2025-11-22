<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('kisiler') && Schema::hasColumn('kisiler', 'status')) {
            Schema::table('kisiler', function (Blueprint $table) {
                $table->string('status')->default('Aktif')->change();
            });
        }
        if (Schema::hasTable('talepler') && Schema::hasColumn('talepler', 'emlak_tipi')) {
            Schema::table('talepler', function (Blueprint $table) {
                $table->string('emlak_tipi')->default('Genel')->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kisiler') && Schema::hasColumn('kisiler', 'status')) {
            Schema::table('kisiler', function (Blueprint $table) {
                $table->string('status')->nullable()->default(null)->change();
            });
        }
        if (Schema::hasTable('talepler') && Schema::hasColumn('talepler', 'emlak_tipi')) {
            Schema::table('talepler', function (Blueprint $table) {
                $table->string('emlak_tipi')->nullable()->default(null)->change();
            });
        }
    }
};