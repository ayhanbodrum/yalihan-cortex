<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7: TCMB integration - Exchange rates table
     */
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();

            // TCMB fields
            $table->string('currency_code', 10)->comment('USD, EUR, GBP, etc.');
            $table->date('rate_date')->comment('Kur tarihi');
            $table->decimal('rate_to_try', 10, 4)->comment('1 birim = X TRY');
            $table->decimal('buying_rate', 10, 4)->comment('Alış kuru');
            $table->decimal('selling_rate', 10, 4)->comment('Satış kuru');
            $table->string('source', 50)->default('TCMB')->comment('Veri kaynağı');

            // Legacy fields (compatibility)
            $table->string('base_currency', 10)->nullable();
            $table->string('currency', 10)->nullable();
            $table->decimal('buy_rate', 10, 4)->nullable();
            $table->decimal('sell_rate', 10, 4)->nullable();
            $table->decimal('mid_rate', 10, 4)->nullable();
            $table->string('provider', 50)->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->boolean('status')->default(true);

            $table->timestamps();

            // Indexes
            $table->index(['currency_code', 'rate_date']);
            $table->index('rate_date');
            $table->unique(['currency_code', 'rate_date']); // Bir gün için bir kur
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
