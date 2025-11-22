<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ilanlar')) {
            return;
        }

        Schema::table('ilanlar', function (Blueprint $table) {
            if (!Schema::hasColumn('ilanlar', 'crm_only')) {
                $table->boolean('crm_only')->default(false)->after('status');
                $table->index('crm_only');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('ilanlar')) {
            return;
        }

        Schema::table('ilanlar', function (Blueprint $table) {
            if (Schema::hasColumn('ilanlar', 'crm_only')) {
                $table->dropIndex(['crm_only']);
                $table->dropColumn('crm_only');
            }
        });
    }
};