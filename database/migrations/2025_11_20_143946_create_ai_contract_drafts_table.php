<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AI Sözleşme Taslakları Tablosu
 *
 * Context7 Standardı: C7-AI-CONTRACT-DRAFTS-2025-11-20
 *
 * Bu tablo, AI tarafından üretilen sözleşme taslaklarını saklar.
 * Tüm taslaklar onaylanmadan kullanılmaz.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('ai_contract_drafts')) {
            return;
        }

        Schema::create('ai_contract_drafts', function (Blueprint $table) {
            $table->id();

            // Contract Type (kira, satis)
            $table->string('contract_type', 50);

            // İlişkiler
            $table->foreignId('property_id')->nullable()->constrained('ilanlar')->onDelete('set null');
            $table->foreignId('kisi_id')->nullable()->constrained('kisiler')->onDelete('set null');

            // Context7: Status field (draft, pending_review, approved, rejected)
            $table->string('status', 20)->default('draft');

            // Content
            $table->longText('content')->nullable();

            // AI Metadata
            $table->string('ai_model_used', 50)->nullable();
            $table->timestamp('ai_generated_at')->nullable();

            // Onay Bilgileri
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('contract_type');
            $table->index('property_id');
            $table->index('kisi_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_contract_drafts');
    }
};
