<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('talepler')) {
            Schema::table('talepler', function (Blueprint $table) {
                if (Schema::hasColumn('talepler', 'talep_tipi')) {
                    $table->string('talep_tipi')->default('Genel')->change();
                }
                if (Schema::hasColumn('talepler', 'kisi_id')) {
                    $table->unsignedBigInteger('kisi_id')->nullable()->change();
                }
                if (Schema::hasColumn('talepler', 'danisman_id')) {
                    $table->unsignedBigInteger('danisman_id')->nullable()->change();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('talepler')) {
            Schema::table('talepler', function (Blueprint $table) {
                if (Schema::hasColumn('talepler', 'talep_tipi')) {
                    $table->string('talep_tipi')->nullable(false)->default(null)->change();
                }
                if (Schema::hasColumn('talepler', 'kisi_id')) {
                    $table->unsignedBigInteger('kisi_id')->nullable(false)->change();
                }
                if (Schema::hasColumn('talepler', 'danisman_id')) {
                    $table->unsignedBigInteger('danisman_id')->nullable(false)->change();
                }
            });
        }
    }
};
