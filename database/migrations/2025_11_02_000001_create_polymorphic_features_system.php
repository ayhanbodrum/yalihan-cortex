<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Feature Categories (Özellik Kategorileri)
        Schema::create('feature_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('type', 50)->nullable(); // arsa, konut, yazlik, ticari
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->integer('order')->default(0);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['type', 'enabled']);
            $table->index('slug');
        });

        // 2. Features (Tüm Özellikler)
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('feature_categories')->nullOnDelete();
            
            // Basic Info
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            
            // Field Configuration
            $table->string('field_type', 50)->default('text'); // text, number, select, checkbox, radio, etc.
            $table->json('field_options')->nullable(); // For select/radio options
            $table->string('field_unit', 20)->nullable(); // m², TL, adet, etc.
            $table->string('field_icon', 50)->nullable();
            
            // Validation Rules
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(true);
            $table->boolean('is_searchable')->default(false);
            $table->string('validation_rules')->nullable(); // min:1|max:100
            
            // AI Integration
            $table->boolean('ai_auto_fill')->default(false);
            $table->boolean('ai_suggestion')->default(false);
            $table->boolean('ai_calculation')->default(false);
            $table->text('ai_prompt')->nullable();
            
            // Display & Order
            $table->integer('order')->default(0);
            $table->boolean('enabled')->default(true);
            $table->boolean('show_in_listing')->default(true);
            $table->boolean('show_in_detail')->default(true);
            $table->boolean('show_in_filter')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['category_id', 'enabled']);
            $table->index(['field_type', 'enabled']);
            $table->index('slug');
            $table->index(['is_filterable', 'enabled']);
        });

        // 3. Feature Assignments (Polymorphic İlişkiler)
        Schema::create('feature_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained('features')->cascadeOnDelete();
            
            // Polymorphic relationship
            $table->morphs('assignable'); // assignable_type, assignable_id
            
            // Assignment Configuration
            $table->text('value')->nullable(); // Actual value if set
            $table->boolean('is_required')->default(false); // Override feature's is_required
            $table->boolean('is_visible')->default(true);
            $table->integer('order')->default(0);
            
            // Conditional Logic
            $table->json('conditional_logic')->nullable(); // Show if X field = Y
            $table->string('group_name', 100)->nullable(); // Group fields together
            
            $table->timestamps();

            // Indexes (morphs() already creates index for assignable_type, assignable_id)
            $table->index(['feature_id', 'assignable_type']);
            $table->unique(['feature_id', 'assignable_type', 'assignable_id'], 'feature_assignment_unique');
        });

        // 4. Feature Values (Gerçek Değerler - İlanlar için)
        Schema::create('feature_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained('features')->cascadeOnDelete();
            
            // Polymorphic relationship (hangi kayıt için)
            $table->morphs('valuable'); // valuable_type, valuable_id (Ilan model)
            
            // Value storage
            $table->text('value')->nullable(); // Actual value
            $table->string('value_type', 20)->default('string'); // string, integer, float, boolean, json
            $table->json('meta')->nullable(); // Extra metadata
            
            $table->timestamps();

            // Indexes (morphs() already creates index for valuable_type, valuable_id)
            $table->index(['feature_id', 'valuable_type']);
            $table->index('value_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_values');
        Schema::dropIfExists('feature_assignments');
        Schema::dropIfExists('features');
        Schema::dropIfExists('feature_categories');
    }
};

