<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7: Market Intelligence - Pazar İstihbaratı
     * Dış kaynaklardan (Sahibinden, Hepsiemlak, Emlakjet) çekilecek piyasa verilerini saklamak için
     * 
     * NOT: Bu migration market_intelligence veritabanında çalışır
     */
    public function up(): void
    {
        // market_intelligence connection'ını kullan
        Schema::connection('market_intelligence')->create('market_listings', function (Blueprint $table) {
            $table->id();

            // Kaynak bilgisi
            $table->string('source', 50)->comment('Kaynak: sahibinden, hepsiemlak, emlakjet');
            $table->string('external_id', 255)->comment('İlanın o sitedeki ID\'si');
            $table->string('url', 500)->nullable()->comment('İlan linki');

            // Temel bilgiler
            $table->string('title', 500)->comment('İlan başlığı');
            $table->decimal('price', 15, 2)->nullable()->comment('Fiyat');
            $table->string('currency', 10)->default('TRY')->comment('Para birimi');

            // Lokasyon (String olarak - Context7: il_id, ilce_id, mahalle_id kullanılmıyor çünkü dış kaynak)
            $table->string('location_il', 100)->nullable()->comment('İl adı');
            $table->string('location_ilce', 100)->nullable()->comment('İlçe adı');
            $table->string('location_mahalle', 100)->nullable()->comment('Mahalle adı');

            // Özellikler
            $table->integer('m2_brut')->nullable()->comment('Brüt metrekare');
            $table->integer('m2_net')->nullable()->comment('Net metrekare');
            $table->string('room_count', 20)->nullable()->comment('Oda sayısı (örn: 3+1)');

            // Tarih bilgileri
            $table->date('listing_date')->nullable()->comment('İlan tarihi');
            $table->timestamp('last_seen_at')->nullable()->comment('En son ne zaman kontrol ettik?');

            // Context7: status tinyInteger (boolean) - 1: Yayında, 0: Kalktı/Satıldı
            $table->tinyInteger('status')->default(1)->comment('1: Yayında, 0: Kalktı/Satıldı');

            // JSON alanlar
            $table->json('snapshot_data')->nullable()->comment('İlanın o anki tüm ham verisi');
            $table->json('price_history')->nullable()->comment('Fiyat değişim geçmişi: [{date: "...", price: ...}]');

            $table->timestamps();

            // Index'ler
            $table->index('source');
            $table->index('external_id');
            $table->index(['source', 'external_id']); // Unique constraint için composite index
            $table->index('status');
            $table->index('last_seen_at');
            $table->index(['location_il', 'location_ilce']);

            // Unique constraint: Aynı kaynaktan aynı external_id sadece bir kez olabilir
            $table->unique(['source', 'external_id'], 'market_listings_source_external_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('market_intelligence')->dropIfExists('market_listings');
    }
};
