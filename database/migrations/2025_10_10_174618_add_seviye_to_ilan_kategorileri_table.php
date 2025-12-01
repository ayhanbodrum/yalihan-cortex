<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ilan_kategorileri')) {
            return;
        }

        Schema::table('ilan_kategorileri', function (Blueprint $table) {
            if (! Schema::hasColumn('ilan_kategorileri', 'seviye')) {
                $table->integer('seviye')->default(1)->after('parent_id');
                $table->index('seviye');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ilan_kategorileri')) {
            return;
        }

        Schema::table('ilan_kategorileri', function (Blueprint $table) {
            if (Schema::hasColumn('ilan_kategorileri', 'seviye')) {
                $table->dropColumn('seviye');
            }
        });
    }
};
