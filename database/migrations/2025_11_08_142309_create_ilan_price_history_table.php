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
        Schema::create('ilan_price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ilan_id')->constrained('ilanlar')->onDelete('cascade');
            $table->decimal('old_price', 15, 2)->nullable()->comment('Eski fiyat');
            $table->decimal('new_price', 15, 2)->comment('Yeni fiyat');
            $table->string('currency', 3)->default('TRY')->comment('Para birimi');
            $table->string('change_reason', 100)->nullable()->comment('Değişiklik sebebi');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('additional_data')->nullable()->comment('Ek veriler');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturulma tarihi');

            // Indexes
            $table->index(['ilan_id', 'created_at'], 'idx_ilan_date');
            $table->index('changed_by', 'idx_changed_by');
            $table->index('currency', 'idx_currency');
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ilan_price_history');
    }
};
