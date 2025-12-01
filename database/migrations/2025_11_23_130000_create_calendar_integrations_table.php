<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('provider');
            $table->string('external_calendar_id')->nullable();
            $table->text('access_token_encrypted')->nullable();
            $table->text('refresh_token_encrypted')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_integrations');
    }
};
