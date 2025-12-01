<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ilan_takvim_sync')) {
            Schema::table('ilan_takvim_sync', function (Blueprint $table) {
                if (! Schema::hasColumn('ilan_takvim_sync', 'ical_url')) {
                    $table->string('ical_url')->nullable()->after('external_listing_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ilan_takvim_sync')) {
            Schema::table('ilan_takvim_sync', function (Blueprint $table) {
                if (Schema::hasColumn('ilan_takvim_sync', 'ical_url')) {
                    $table->dropColumn('ical_url');
                }
            });
        }
    }
};
