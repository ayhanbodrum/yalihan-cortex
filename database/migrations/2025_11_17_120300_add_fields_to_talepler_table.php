<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('talepler')) {
            Schema::table('talepler', function (Blueprint $table) {
                if (!Schema::hasColumn('talepler', 'baslik')) {
                    $table->string('baslik')->nullable();
                }
                if (!Schema::hasColumn('talepler', 'aciklama')) {
                    $table->text('aciklama')->nullable();
                }
                if (!Schema::hasColumn('talepler', 'status')) {
                    $table->string('status')->default('Aktif');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('talepler')) {
            Schema::table('talepler', function (Blueprint $table) {
                if (Schema::hasColumn('talepler', 'baslik')) {
                    $table->dropColumn('baslik');
                }
                if (Schema::hasColumn('talepler', 'aciklama')) {
                    $table->dropColumn('aciklama');
                }
                if (Schema::hasColumn('talepler', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};