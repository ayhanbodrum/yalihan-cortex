<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_behavior_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('event_type');
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_behavior_events');
    }
};
