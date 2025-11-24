<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('method', 10);
            $table->string('route_name')->nullable()->index();
            $table->string('url', 1024);
            $table->string('ip', 64)->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->string('module')->nullable();
            $table->string('controller')->nullable();
            $table->string('action')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};