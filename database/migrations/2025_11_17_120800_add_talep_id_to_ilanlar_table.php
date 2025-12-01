<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ilanlar') && ! Schema::hasColumn('ilanlar', 'talep_id')) {
            Schema::table('ilanlar', function (Blueprint $table) {
                $table->unsignedBigInteger('talep_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ilanlar') && Schema::hasColumn('ilanlar', 'talep_id')) {
            Schema::table('ilanlar', function (Blueprint $table) {
                $table->dropColumn('talep_id');
            });
        }
    }
};
