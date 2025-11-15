<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etiketler', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('#3B82F6');
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('display_order')->default(0); // Context7: order → display_order
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'display_order']); // Context7: order → display_order
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etiketler');
    }
};
