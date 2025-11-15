<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Feature Assignments - Polymorphic İlişkiler
     *
     * Bu tablo, feature'ların hangi modellere (yayın tipleri, kategoriler vb.) atandığını saklar.
     */
    public function up(): void
    {
        if (!Schema::hasTable('feature_assignments')) {
            Schema::create('feature_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('feature_id')->constrained('features')->cascadeOnDelete();

                // Polymorphic relationship
                $table->morphs('assignable'); // assignable_type, assignable_id

                // Assignment Configuration
                $table->text('value')->nullable(); // Actual value if set
                $table->boolean('is_required')->default(false); // Override feature's is_required
                $table->boolean('is_visible')->default(true);
                $table->integer('display_order')->default(0); // Context7: order → display_order

                // Conditional Logic
                $table->json('conditional_logic')->nullable(); // Show if X field = Y
                $table->string('group_name', 100)->nullable(); // Group fields together

                $table->timestamps();

                // Indexes (morphs() already creates index for assignable_type, assignable_id)
                $table->index(['feature_id', 'assignable_type']);
                $table->unique(['feature_id', 'assignable_type', 'assignable_id'], 'feature_assignment_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_assignments');
    }
};
