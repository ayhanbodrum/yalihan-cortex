<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7 Standardı: C7-REFERANS-MIGRATION-2025-10-11
     */
    public function up(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Referans numarası (benzersiz)
            $table->string('referans_no', 50)->nullable()->unique()->after('ilan_no');

            // Dosya adı (kullanıcı dostu)
            $table->string('dosya_adi', 255)->nullable()->after('referans_no');

            // Portal ID'ler
            $table->string('sahibinden_id', 50)->nullable()->after('dosya_adi');
            $table->string('emlakjet_id', 50)->nullable()->after('sahibinden_id');
            $table->string('hepsiemlak_id', 50)->nullable()->after('emlakjet_id');
            $table->string('zingat_id', 50)->nullable()->after('hepsiemlak_id');
            $table->string('hurriyetemlak_id', 50)->nullable()->after('zingat_id');

            // Portal sync durumu (JSON)
            $table->json('portal_sync_status')->nullable()->after('hurriyetemlak_id');

            // Portal özel fiyatlar (JSON)
            $table->json('portal_pricing')->nullable()->after('portal_sync_status');

            // Indeksler
            $table->index('referans_no', 'idx_referans_no');
            $table->index('sahibinden_id', 'idx_sahibinden_id');
            $table->index('emlakjet_id', 'idx_emlakjet_id');
            $table->index('hepsiemlak_id', 'idx_hepsiemlak_id');
            $table->index('zingat_id', 'idx_zingat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            $table->dropIndex('idx_referans_no');
            $table->dropIndex('idx_sahibinden_id');
            $table->dropIndex('idx_emlakjet_id');
            $table->dropIndex('idx_hepsiemlak_id');
            $table->dropIndex('idx_zingat_id');

            $table->dropColumn([
                'referans_no',
                'dosya_adi',
                'sahibinden_id',
                'emlakjet_id',
                'hepsiemlak_id',
                'zingat_id',
                'hurriyetemlak_id',
                'portal_sync_status',
                'portal_pricing',
            ]);
        });
    }
};
