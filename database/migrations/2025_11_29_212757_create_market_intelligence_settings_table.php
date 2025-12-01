<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7: Market Intelligence Settings - Pazar İstihbaratı Bölge Ayarları
     * Kullanıcıların hangi bölgelerden veri çekileceğini belirler
     * 
     * NOT: Bu migration ana veritabanında çalışır (market_intelligence değil)
     */
    public function up(): void
    {
        Schema::create('market_intelligence_settings', function (Blueprint $table) {
            $table->id();

            // Kullanıcı bazlı ayar (NULL = Global ayar, tüm kullanıcılar için)
            $table->unsignedBigInteger('user_id')->nullable()->comment('Kullanıcı bazlı ayar (NULL = Global)');

            // Lokasyon bilgileri (Context7: il_id, ilce_id, mahalle_id)
            $table->unsignedBigInteger('il_id')->comment('İl ID');
            $table->unsignedBigInteger('ilce_id')->nullable()->comment('İlçe ID (NULL = Tüm ilçeler)');
            $table->unsignedBigInteger('mahalle_id')->nullable()->comment('Mahalle ID (NULL = Tüm mahalleler)');

            // Context7: status tinyInteger (boolean) - 1: Aktif, 0: Pasif
            $table->tinyInteger('status')->default(1)->comment('1: Aktif (çekilecek), 0: Pasif (çekilmeyecek)');

            // Öncelik (yüksek = önce çekilir)
            $table->integer('priority')->default(0)->comment('Öncelik (1-10: Yüksek, 11-50: Orta, 51-100: Düşük)');

            $table->timestamps();

            // Index'ler
            $table->index('user_id');
            $table->index('il_id');
            $table->index('status');
            $table->index('priority');

            // Unique constraint: Aynı kullanıcı için aynı lokasyon tekrar edemez
            $table->unique(['user_id', 'il_id', 'ilce_id', 'mahalle_id'], 'unique_location_per_user');

            // Foreign key'ler
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('il_id')->references('id')->on('iller')->onDelete('cascade');
            $table->foreign('ilce_id')->references('id')->on('ilceler')->onDelete('cascade');
            $table->foreign('mahalle_id')->references('id')->on('mahalleler')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_intelligence_settings');
    }
};
