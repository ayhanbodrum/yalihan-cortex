<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('component');
            $table->string('status')->default('ok');
            $table->json('payload')->nullable();
            $table->decimal('threshold', 8, 2)->nullable();
            $table->timestamps();
            $table->index(['component', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_alerts');
    }
};
