<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7: Rename Musteri* tables to Kisi* (final step)
     *
     * This migration completes the Musteri → Kisi terminology standardization
     */
    public function up(): void
    {
        // 1. Rename musteri_aktiviteler → kisi_aktiviteler (if exists)
        if (Schema::hasTable('musteri_aktiviteler')) {
            Schema::rename('musteri_aktiviteler', 'kisi_aktiviteler');
            echo "✅ Renamed: musteri_aktiviteler → kisi_aktiviteler\n";
        }

        // 2. Rename musteri_takip → kisi_takip (if exists)
        if (Schema::hasTable('musteri_takip')) {
            Schema::rename('musteri_takip', 'kisi_takip');
            echo "✅ Renamed: musteri_takip → kisi_takip\n";
        }

        // 3. Rename musteri_notlar → kisi_notlar (if exists)
        if (Schema::hasTable('musteri_notlar')) {
            Schema::rename('musteri_notlar', 'kisi_notlar');
            echo "✅ Renamed: musteri_notlar → kisi_notlar\n";
        }

        // 4. Check musteri_etiketler (might already be renamed to etiketler)
        if (Schema::hasTable('musteri_etiketler') && ! Schema::hasTable('etiketler')) {
            Schema::rename('musteri_etiketler', 'etiketler');
            echo "✅ Renamed: musteri_etiketler → etiketler\n";
        }

        // 5. Update model references in polymorphic tables (if any)
        $polymorphicUpdates = [
            ['table' => 'activities', 'column' => 'subject_type', 'old' => 'App\\Models\\MusteriAktivite', 'new' => 'App\\Models\\KisiAktivite'],
            ['table' => 'activity_log', 'column' => 'subject_type', 'old' => 'App\\Models\\MusteriAktivite', 'new' => 'App\\Models\\KisiAktivite'],
        ];

        foreach ($polymorphicUpdates as $update) {
            if (Schema::hasTable($update['table']) && Schema::hasColumn($update['table'], $update['column'])) {
                DB::table($update['table'])
                    ->where($update['column'], $update['old'])
                    ->update([$update['column'] => $update['new']]);
                echo "✅ Updated polymorphic: {$update['table']}.{$update['column']}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback table renames
        if (Schema::hasTable('kisi_aktiviteler')) {
            Schema::rename('kisi_aktiviteler', 'musteri_aktiviteler');
        }

        if (Schema::hasTable('kisi_takip')) {
            Schema::rename('kisi_takip', 'musteri_takip');
        }

        if (Schema::hasTable('kisi_notlar')) {
            Schema::rename('kisi_notlar', 'musteri_notlar');
        }

        if (Schema::hasTable('etiketler') && ! Schema::hasTable('musteri_etiketler')) {
            Schema::rename('etiketler', 'musteri_etiketler');
        }
    }
};
