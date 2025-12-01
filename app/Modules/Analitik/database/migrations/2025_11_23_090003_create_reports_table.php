<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->json('payload')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
