<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ilan_takvim_sync', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ilan_id')->constrained('ilanlar')->cascadeOnDelete();
            $table->enum('platform', ['airbnb', 'booking_com', 'google_calendar', 'calendar_dot_com'])->default('airbnb');
            $table->string('external_calendar_id')->nullable();
            $table->string('external_listing_id')->nullable();
            $table->boolean('sync_enabled')->default(false);
            $table->boolean('auto_sync')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamp('next_sync_at')->nullable();
            $table->integer('sync_interval_minutes')->default(60);
            $table->text('sync_settings')->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->enum('sync_status', ['active', 'paused', 'failed', 'disconnected'])->default('disconnected');
            $table->string('last_error')->nullable();
            $table->timestamp('last_error_at')->nullable();
            $table->integer('sync_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->timestamps();

            $table->unique(['ilan_id', 'platform']);
            $table->index(['platform', 'sync_enabled']);
            $table->index(['last_sync_at', 'next_sync_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ilan_takvim_sync');
    }
};
