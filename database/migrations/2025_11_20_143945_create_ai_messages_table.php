<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AI Mesaj Taslakları Tablosu
 *
 * Context7 Standardı: C7-AI-MESSAGES-2025-11-20
 *
 * Bu tablo, AI tarafından üretilen mesaj taslaklarını saklar.
 * Tüm mesajlar onaylanmadan gönderilmez.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('ai_messages')) {
            return;
        }

        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();

            // İlişkiler
            $table->unsignedBigInteger('conversation_id')->nullable();
            if (Schema::hasTable('ai_conversations')) {
                $table->foreign('conversation_id')->references('id')->on('ai_conversations')->onDelete('cascade');
            }

            $table->unsignedBigInteger('communication_id')->nullable();
            if (Schema::hasTable('communications')) {
                $table->foreign('communication_id')->references('id')->on('communications')->onDelete('set null');
            }

            // Channel (telegram, whatsapp, instagram, email, web)
            $table->string('channel', 20);

            // Role (user, assistant)
            $table->string('role', 20)->default('assistant');

            // Content
            $table->text('content');

            // Context7: Status field (draft, pending_approval, approved, sent)
            $table->string('status', 20)->default('draft');

            // AI Metadata
            $table->string('ai_model_used', 50)->nullable();
            $table->timestamp('ai_generated_at')->nullable();

            // Onay Bilgileri
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('channel');
            $table->index('conversation_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
    }
};
