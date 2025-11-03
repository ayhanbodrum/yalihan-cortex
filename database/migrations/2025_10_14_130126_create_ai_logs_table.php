<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7 Standardı: C7-AI-LOGS-TABLE-2025-10-14
     */
    public function up(): void
    {
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();

            // Provider bilgisi
            $table->string('provider', 50)->index(); // google, openai, claude, ollama, deepseek

            // Request bilgileri
            $table->string('request_type', 50)->nullable(); // title, description, features, analysis
            $table->string('content_type', 50)->nullable(); // ilan, kategori, kisi
            $table->unsignedBigInteger('content_id')->nullable(); // İlgili içerik ID

            // Response bilgileri
            $table->string('status', 20)->index(); // success, failed, timeout, error
            $table->integer('response_time')->nullable(); // milliseconds
            $table->decimal('cost', 10, 6)->nullable(); // USD (6 decimal for precision)
            $table->integer('tokens_used')->nullable();

            // Data storage
            $table->text('request_data')->nullable(); // JSON
            $table->longText('response_data')->nullable(); // JSON
            $table->text('error_message')->nullable();

            // Context7: Kullanıcı tracking
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Metadata
            $table->string('model', 100)->nullable(); // gpt-4, gemini-pro, etc.
            $table->string('version', 20)->nullable(); // API version
            $table->ipAddress('ip_address')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index('created_at');
            $table->index(['provider', 'status']);
            $table->index(['content_type', 'content_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
    }
};
