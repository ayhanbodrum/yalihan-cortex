<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('yazlik_details', function (Blueprint $table) {
            $table->integer('oda_sayisi')->nullable()->after('max_misafir')->comment('Oda sayısı');
            $table->integer('banyo_sayisi')->nullable()->after('oda_sayisi')->comment('Banyo sayısı');
            $table->integer('yatak_sayisi')->nullable()->after('banyo_sayisi')->comment('Yatak sayısı');

            $table->json('yatak_turleri')->nullable()->after('yatak_sayisi')->comment('Yatak türleri array');

            $table->boolean('carsaf_dahil')->default(false)->after('yatak_turleri')->comment('Çarşaf dahil mi');
            $table->boolean('havlu_dahil')->default(false)->after('carsaf_dahil')->comment('Havlu dahil mi');
            $table->boolean('internet_dahil')->default(false)->after('elektrik_dahil')->comment('İnternet dahil mi');
            $table->boolean('klima_var')->default(false)->after('internet_dahil')->comment('Klima var mı');

            $table->integer('restoran_mesafe')->nullable()->after('klima_var')->comment('Restoran mesafe (km)');
            $table->integer('market_mesafe')->nullable()->after('restoran_mesafe')->comment('Market mesafe (km)');
            $table->integer('deniz_mesafe')->nullable()->after('market_mesafe')->comment('Deniz mesafe (km)');
            $table->integer('merkez_mesafe')->nullable()->after('deniz_mesafe')->comment('Merkez mesafe (km)');

            $table->string('havuz_boyut_en')->nullable()->after('havuz_derinlik')->comment('Havuz genişlik (m)');
            $table->string('havuz_boyut_boy')->nullable()->after('havuz_boyut_en')->comment('Havuz uzunluk (m)');

            $table->boolean('bahce_var')->default(false)->after('havuz_boyut_boy')->comment('Bahçe var mı');
            $table->boolean('tv_var')->default(false)->after('bahce_var')->comment('TV var mı');
            $table->boolean('barbeku_var')->default(false)->after('tv_var')->comment('Barbekü var mı');
            $table->boolean('sezlong_var')->default(false)->after('barbeku_var')->comment('Şezlong var mı');
            $table->boolean('bahce_masasi_var')->default(false)->after('sezlong_var')->comment('Bahçe masası var mı');

            $table->string('manzara')->nullable()->after('bahce_masasi_var')->comment('Manzara türü');
            $table->json('ozel_isaretler')->nullable()->after('manzara')->comment('Özel işaretler array');

            $table->string('ev_tipi')->nullable()->after('ozel_isaretler')->comment('Ev tipi (villa, bungalov, etc.)');
            $table->string('ev_konsepti')->nullable()->after('ev_tipi')->comment('Ev konsepti');
        });
    }

    public function down(): void
    {
        Schema::table('yazlik_details', function (Blueprint $table) {
            $table->dropColumn([
                'oda_sayisi',
                'banyo_sayisi',
                'yatak_sayisi',
                'yatak_turleri',
                'carsaf_dahil',
                'havlu_dahil',
                'internet_dahil',
                'klima_var',
                'restoran_mesafe',
                'market_mesafe',
                'deniz_mesafe',
                'merkez_mesafe',
                'havuz_boyut_en',
                'havuz_boyut_boy',
                'bahce_var',
                'tv_var',
                'barbeku_var',
                'sezlong_var',
                'bahce_masasi_var',
                'manzara',
                'ozel_isaretler',
                'ev_tipi',
                'ev_konsepti',
            ]);
        });
    }
};
