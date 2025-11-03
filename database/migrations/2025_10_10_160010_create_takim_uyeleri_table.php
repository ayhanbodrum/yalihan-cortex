<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('takim_uyeleri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('pozisyon')->nullable();
            $table->string('departman')->nullable();
            $table->integer('performans_skoru')->default(0);
            $table->date('ise_baslama_tarihi')->nullable();
            $table->string('status')->default('Aktif');
            $table->text('notlar')->nullable();
            $table->timestamps();

            $table->index(['status', 'departman']);
            $table->index('user_id');
            $table->index('performans_skoru');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('takim_uyeleri');
    }
};
