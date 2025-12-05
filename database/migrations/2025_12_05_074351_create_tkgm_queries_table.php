<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Context7 Compliance Migration Template
 *
 * ⚠️ CONTEXT7 PERMANENT STANDARDS:
 * - ALWAYS use 'display_order' field, NEVER use 'order'
 * - ALWAYS use 'status' field, NEVER use 'enabled', 'aktif', 'is_active'
 * - Pre-commit hook will BLOCK migrations with forbidden patterns
 * - This is a PERMANENT STANDARD - NO EXCEPTIONS
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tkgm_queries', function (Blueprint $table) {
            $table->id();

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // 🏗️ ADA/PARSEL BİLGİLERİ
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            $table->string('ada', 20)->nullable()->comment('Ada numarası');
            $table->string('parsel', 20)->nullable()->comment('Parsel numarası');

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // 📍 LOKASYON BİLGİLERİ
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            $table->unsignedBigInteger('il_id')->nullable()->comment('İl ID (iller tablosu)');
            $table->unsignedBigInteger('ilce_id')->nullable()->comment('İlçe ID (ilceler tablosu)');
            $table->unsignedBigInteger('mahalle_id')->nullable()->comment('Mahalle ID (mahalleler tablosu)');

            // Koordinatlar (GPS)
            $table->decimal('enlem', 10, 8)->nullable()->comment('Latitude (GPS koordinat)');
            $table->decimal('boylam', 11, 8)->nullable()->comment('Longitude (GPS koordinat)');

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // 🏗️ TKGM TEKNİK VERİLERİ
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            $table->decimal('alan_m2', 10, 2)->nullable()->comment('Parsel alanı (m²)');
            $table->decimal('kaks', 4, 2)->nullable()->comment('KAKS değeri (örn: 0.50)');
            $table->integer('taks')->nullable()->comment('TAKS değeri');
            $table->string('imar_statusu', 100)->nullable()->comment('İmar durumu (örn: İmarlı, Plan İçi)');
            $table->string('nitelik', 50)->nullable()->comment('Parsel niteliği');
            $table->integer('gabari')->nullable()->comment('Gabari (kat sayısı)');

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // 💰 İLAN & SATIŞ BİLGİLERİ (Opsiyonel)
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            $table->unsignedBigInteger('ilan_id')->nullable()->comment('İlgili ilan ID (ilanlar tablosu)');
            $table->decimal('satis_fiyati', 15, 2)->nullable()->comment('Satış fiyatı (TL) - ilan satıldıysa');
            $table->date('satis_tarihi')->nullable()->comment('Satış tarihi - öğrenme için kritik');
            $table->integer('satis_suresi_gun')->nullable()->comment('Kaç günde satıldı (ilan_created → satış)');

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // 📊 META & TRAKİNG
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            $table->string('query_source', 50)->default('wizard')->comment('Sorgu kaynağı: wizard, calculator, api');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Sorguyu yapan kullanıcı (users tablosu)');
            $table->timestamp('queried_at')->useCurrent()->comment('Sorgu zamanı');

            // TKGM API raw response (debugging & learning için)
            $table->json('tkgm_raw_data')->nullable()->comment('TKGM API ham yanıtı (JSON)');

            // ✅ CONTEXT7 PERMANENT STANDARD: Status field
            $table->tinyInteger('status')->default(1)->comment('0=inactive, 1=active (Context7 standard)');

            $table->timestamps();
            $table->softDeletes();

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // 📇 INDEXES (Performance için kritik)
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            $table->index(['il_id', 'ilce_id'], 'idx_location');
            $table->index(['ada', 'parsel'], 'idx_ada_parsel');
            $table->index('kaks', 'idx_kaks');
            $table->index('alan_m2', 'idx_alan');
            $table->index('imar_statusu', 'idx_imar');
            $table->index('satis_fiyati', 'idx_satis_fiyati');
            $table->index('queried_at', 'idx_queried_at');
            $table->index('user_id', 'idx_user_id');
            $table->index('ilan_id', 'idx_ilan_id');

            // Foreign keys (opsiyonel - relationship için)
            $table->foreign('il_id')->references('id')->on('iller')->onDelete('set null');
            $table->foreign('ilce_id')->references('id')->on('ilceler')->onDelete('set null');
            $table->foreign('ilan_id')->references('id')->on('ilanlar')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkgm_queries');
    }
};
