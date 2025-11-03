<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->default('boolean'); // boolean, text, number, select
            $table->json('options')->nullable(); // For select type
            $table->string('unit')->nullable(); // For number type (m², adet, etc.)
            $table->unsignedBigInteger('feature_category_id')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(true);
            $table->boolean('is_searchable')->default(true);
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('feature_category_id')->references('id')->on('feature_categories')->onDelete('set null');
            $table->index(['status', 'order']);
            $table->index(['feature_category_id', 'status']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};