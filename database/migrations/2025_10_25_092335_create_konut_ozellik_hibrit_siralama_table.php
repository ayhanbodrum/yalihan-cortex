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
        Schema::create('konut_ozellik_hibrit_siralama', function (Blueprint $table) {
            $table->id();
            $table->string('ozellik_adi', 100);
            $table->string('ozellik_slug', 100)->unique();
            $table->integer('kullanim_sikligi')->default(0);
            $table->decimal('ai_oneri_yuzdesi', 5, 2)->default(0.00);
            $table->decimal('kullanici_tercih_yuzdesi', 5, 2)->default(0.00);
            $table->decimal('hibrit_skor', 8, 2)->default(0.00);
            $table->enum('onem_seviyesi', ['cok_onemli', 'onemli', 'orta_onemli', 'dusuk_onemli'])->default('orta_onemli');
            $table->integer('siralama')->default(0);
            $table->boolean('status')->default(true);
            $table->text('aciklama')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konut_ozellik_hibrit_siralama');
    }
};
