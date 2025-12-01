<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AI Konuşmalar Tablosu
 *
 * Context7 Standardı: C7-AI-CONVERSATIONS-2025-11-20
 *
 * Bu tablo, AI ile yapılan konuşmaları saklar.
 * Messages JSON formatında saklanır.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('ai_conversations')) {
            return;
        }

        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();

            // İlişkiler
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Channel (telegram, whatsapp, instagram, email, web)
            $table->string('channel', 20);

            // Messages (JSON)
            $table->json('messages')->nullable();

            // Context7: Status field (active, closed, archived)
            $table->string('status', 20)->default('active');

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('channel');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};
