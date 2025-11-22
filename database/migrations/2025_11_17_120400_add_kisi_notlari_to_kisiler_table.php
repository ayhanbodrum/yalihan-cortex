<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('kisiler') && !Schema::hasColumn('kisiler', 'kisi_notlari')) {
            Schema::table('kisiler', function (Blueprint $table) {
                $table->text('kisi_notlari')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kisiler') && Schema::hasColumn('kisiler', 'kisi_notlari')) {
            Schema::table('kisiler', function (Blueprint $table) {
                $table->dropColumn('kisi_notlari');
            });
        }
    }
};