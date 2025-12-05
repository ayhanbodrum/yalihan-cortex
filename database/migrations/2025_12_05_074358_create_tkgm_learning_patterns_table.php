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
        Schema::create('tkgm_learning_patterns', function (Blueprint $table) {
            $table->id();

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // 🧠 PATTERN TİPİ & LOKASYON
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            $table->string('pattern_type', 50)->comment('Pattern tipi: price_kaks, location_premium, imar_effect, velocity, roi');
            $table->unsignedBigInteger('il_id')->nullable()->comment('İl ID (NULL = tüm Türkiye)');
            $table->unsignedBigInteger('ilce_id')->nullable()->comment('İlçe ID (NULL = il geneli)');
            $table->unsignedBigInteger('mahalle_id')->nullable()->comment('Mahalle ID (NULL = ilçe geneli)');

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // 📊 PATTERN VERİLERİ (JSON)
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // Örnek pattern_data structure:
            // {
            //   "kaks_values": [0.30, 0.40, 0.50, 0.60, 0.80],
            //   "avg_prices": [5200, 6700, 6850, 5550, 5100],
            //   "sample_counts": [3, 8, 15, 6, 2],
            //   "velocity_days": [68, 58, 52, 48, 42]
            // }
            $table->json('pattern_data')->comment('Pattern verileri (JSON format)');

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // 📈 İSTATİSTİKLER & GÜVENİLİRLİK
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            $table->integer('sample_count')->default(0)->comment('Kaç veri noktasından öğrenildi');
            $table->decimal('confidence_level', 5, 2)->default(0.00)->comment('Güven seviyesi (%0-100)');
            $table->timestamp('last_calculated_at')->useCurrent()->comment('Pattern son ne zaman hesaplandı');
            $table->timestamp('last_updated_at')->useCurrent()->comment('Pattern son ne zaman güncellendi');

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // 🎯 PERFORMANCE METRİKLERİ
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            $table->integer('prediction_count')->default(0)->comment('Bu pattern kaç kez kullanıldı');
            $table->decimal('prediction_accuracy', 5, 2)->nullable()->comment('Tahmin doğruluk oranı (%0-100)');
            $table->integer('successful_predictions')->default(0)->comment('Kaç tahmin doğru çıktı');

            // ✅ CONTEXT7 PERMANENT STANDARD: Status field
            $table->tinyInteger('status')->default(1)->comment('0=inactive, 1=active (Context7 standard)');

            $table->timestamps();
            $table->softDeletes();

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // 📇 INDEXES (Hızlı sorgulama için)
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            $table->index('pattern_type', 'idx_pattern_type');
            $table->index(['il_id', 'ilce_id'], 'idx_location');
            $table->index(['pattern_type', 'il_id', 'ilce_id'], 'idx_pattern_location');
            $table->index('confidence_level', 'idx_confidence');
            $table->index('last_calculated_at', 'idx_last_calculated');
            $table->index('sample_count', 'idx_sample_count');

            // Foreign keys
            $table->foreign('il_id')->references('id')->on('iller')->onDelete('set null');
            $table->foreign('ilce_id')->references('id')->on('ilceler')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkgm_learning_patterns');
    }
};
