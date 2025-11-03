<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ozellik_kategorileri', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('aciklama')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->string('status')->default('Aktif');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['parent_id', 'status']);
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ozellik_kategorileri');
    }
};
