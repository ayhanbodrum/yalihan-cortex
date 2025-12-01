<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_category_analytics', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->string('suggestion_type');
            $table->decimal('confidence_score', 3, 2);
            $table->json('ai_response');
            $table->boolean('user_accepted');
            $table->timestamp('suggested_at');
            $table->timestamps();

            $table->index(['category_id', 'suggested_at']);
            $table->index(['suggestion_type', 'confidence_score']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_category_analytics');
    }
};
