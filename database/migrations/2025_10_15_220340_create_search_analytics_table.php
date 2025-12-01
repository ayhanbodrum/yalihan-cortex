<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('search_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->string('type')->default('unified');
            $table->json('filters')->nullable();
            $table->integer('results_count');
            $table->float('response_time');
            $table->boolean('success');
            $table->ipAddress('ip_address');
            $table->string('user_agent')->nullable();
            $table->timestamp('searched_at');
            $table->timestamps();

            $table->index(['query', 'searched_at']);
            $table->index(['type', 'success']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('search_analytics');
    }
};
