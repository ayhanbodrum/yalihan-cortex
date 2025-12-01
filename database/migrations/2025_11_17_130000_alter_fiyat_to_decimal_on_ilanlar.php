<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ilanlar') && Schema::hasColumn('ilanlar', 'fiyat')) {
            Schema::table('ilanlar', function (Blueprint $table) {
                $table->decimal('fiyat', 18, 2)->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ilanlar') && Schema::hasColumn('ilanlar', 'fiyat')) {
            Schema::table('ilanlar', function (Blueprint $table) {
                $table->string('fiyat')->change();
            });
        }
    }
};
