<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * AI İlan Taslakları Tablosu
 *
 * Context7 Standardı: C7-AI-ILAN-TASLAKLARI-2025-11-20
 *
 * Bu tablo, AI tarafından üretilen ilan taslaklarını saklar.
 * Tüm taslaklar onaylanmadan önce 'draft' durumunda kalır.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Context7: Tablo varlık kontrolü
        if (Schema::hasTable('ai_ilan_taslaklari')) {
            return;
        }

        Schema::create('ai_ilan_taslaklari', function (Blueprint $table) {
            $table->id();

            // İlişkiler
            $table->foreignId('ilan_id')->nullable()->constrained('ilanlar')->onDelete('set null');
            $table->foreignId('danisman_id')->constrained('users')->onDelete('cascade');

            // Context7: Status field (draft, pending_review, approved, published)
            $table->string('status', 20)->default('draft');

            // AI Response (JSON)
            $table->json('ai_response')->nullable();

            // AI Metadata
            $table->string('ai_model_used', 50)->nullable()->comment('AnythingLLM, Ollama, vb.');
            $table->string('ai_prompt_version', 20)->nullable();
            $table->timestamp('ai_generated_at')->nullable();

            // Onay Bilgileri
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('danisman_id');
            $table->index('ilan_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_ilan_taslaklari');
    }
};
