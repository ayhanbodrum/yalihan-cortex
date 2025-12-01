<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Communications Tablosu
 *
 * Context7 Standardı: C7-COMMUNICATIONS-2025-11-20
 *
 * Çok kanallı iletişim kayıtlarını saklar (polymorphic).
 * Telegram, WhatsApp, Instagram, Email, Web form mesajları.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('communications')) {
            return;
        }

        Schema::create('communications', function (Blueprint $table) {
            $table->id();

            // Polymorphic ilişki: Hangi kayıtla ilişkili (ilan, kisi, talep, vb.)
            $table->nullableMorphs('communicable');

            // Kanal bilgisi
            $table->string('channel', 20)->comment('telegram, whatsapp, instagram, email, web');

            // Mesaj bilgileri
            $table->text('message');
            $table->string('sender_name')->nullable();
            $table->string('sender_phone')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_id')->nullable()->comment('Channel-specific ID (telegram_chat_id, etc.)');

            // Durum
            $table->string('status', 20)->default('new')->comment('new, read, replied, closed');
            $table->string('priority', 20)->default('normal')->comment('low, normal, high, urgent');

            // AI Analiz (JSON)
            $table->json('ai_analysis')->nullable()->comment('Müşteri niyeti, bütçe, talep analizi');
            $table->timestamp('ai_analyzed_at')->nullable();

            // İlişkiler
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Indexes (nullableMorphs already creates index for communicable_type and communicable_id)
            $table->index('channel');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
