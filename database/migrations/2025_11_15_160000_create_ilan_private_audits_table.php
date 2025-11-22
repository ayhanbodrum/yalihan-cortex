<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ilan_private_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ilan_id');
            $table->unsignedBigInteger('user_id');
            $table->json('changes');
            $table->timestamps();
            $table->index(['ilan_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ilan_private_audits');
    }
};