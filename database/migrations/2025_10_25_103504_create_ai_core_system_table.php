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
        Schema::create('ai_core_system', function (Blueprint $table) {
            $table->id();
            $table->string('context', 100)->index();
            $table->string('task_type', 50)->index();
            $table->text('prompt_template');
            $table->json('learned_patterns')->nullable();
            $table->decimal('success_rate', 5, 2)->default(0.00);
            $table->integer('usage_count')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['context', 'task_type']);
            $table->index(['context', 'success_rate']);
        });

        Schema::create('ai_learning_data', function (Blueprint $table) {
            $table->id();
            $table->string('context', 100)->index();
            $table->text('input_data');
            $table->text('expected_output');
            $table->text('actual_output')->nullable();
            $table->decimal('accuracy_score', 5, 2)->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->index(['context', 'is_correct']);
        });

        Schema::create('ai_storage', function (Blueprint $table) {
            $table->id();
            $table->string('storage_key')->unique();
            $table->longText('data');
            $table->string('type', 50)->default('pattern');
            $table->string('context', 100)->nullable();
            $table->timestamps();

            $table->index(['type', 'context']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_storage');
        Schema::dropIfExists('ai_learning_data');
        Schema::dropIfExists('ai_core_system');
    }
};
