<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AI Arsa Analizleri Tablosu
 *
 * Context7 Standardı: C7-AI-ARSA-ANALIZ-2025-11-20
 *
 * AI tarafından yapılan arsa analizlerini saklar.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('ai_land_plot_analyses')) {
            Schema::dropIfExists('ai_land_plot_analyses');
        }

        Schema::create('ai_land_plot_analyses', function (Blueprint $table) {
            $table->id();

            // İlan ilişkisi
            $table->foreignId('ilan_id')->constrained('ilanlar')->onDelete('cascade');

            // Analiz türü
            $table->string('analysis_type', 50)->default('comprehensive')->comment('comprehensive, price_analysis, market_analysis, risk_analysis');

            // Analiz sonuçları (JSON)
            $table->json('analysis_data')->nullable()->comment('Detaylı analiz verileri');
            $table->json('recommendations')->nullable()->comment('AI önerileri');
            $table->json('market_data')->nullable()->comment('Market DB\'den toplanan veriler');

            // Skorlar
            $table->decimal('confidence_score', 5, 2)->default(0)->comment('Güven skoru (0-100)');
            $table->decimal('price_score', 5, 2)->nullable()->comment('Fiyat skoru');
            $table->decimal('risk_score', 5, 2)->nullable()->comment('Risk skoru');
            $table->decimal('market_score', 5, 2)->nullable()->comment('Piyasa skoru');

            // Fiyat analizi
            $table->decimal('suggested_price_min', 15, 2)->nullable();
            $table->decimal('suggested_price_max', 15, 2)->nullable();
            $table->decimal('current_price', 15, 2)->nullable();

            // AI Metadata
            $table->string('ai_model_used', 50)->nullable();
            $table->string('ai_prompt_version', 20)->nullable();
            $table->timestamp('ai_generated_at')->nullable();

            // İlişkiler
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Indexes (foreignId already creates index for ilan_id)
            $table->index('analysis_type');
            $table->index('confidence_score');
            $table->index('created_at');
            $table->index(['ilan_id', 'analysis_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_land_plot_analyses');
    }
};
