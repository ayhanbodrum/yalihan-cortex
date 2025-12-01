<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('design_token_usage', function (Blueprint $table) {
            $table->id();
            $table->string('page_name');
            $table->string('component_type');
            $table->json('tokens_used');
            $table->integer('token_count');
            $table->decimal('compliance_score', 3, 2);
            $table->timestamp('analyzed_at');
            $table->timestamps();

            $table->index(['page_name', 'analyzed_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('design_token_usage');
    }
};
