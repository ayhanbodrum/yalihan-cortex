<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ilan_kategorileri', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('aciklama')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('icon')->nullable();
            $table->integer('display_order')->default(0); // Context7: order → display_order
            $table->string('status')->default('Aktif');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['parent_id', 'status']);
            $table->index('display_order'); // Context7: order → display_order
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ilan_kategorileri');
    }
};
