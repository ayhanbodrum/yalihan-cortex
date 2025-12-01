<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_recommendations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('source')->nullable();
            $table->json('recommendation');
            $table->decimal('score', 5, 2)->default(0);
            $table->timestamps();
            $table->index(['user_id', 'source']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_recommendations');
    }
};
