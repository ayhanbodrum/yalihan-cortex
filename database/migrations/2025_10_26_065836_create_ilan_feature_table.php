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
        Schema::create('ilan_feature', function (Blueprint $table) {
            $table->foreignId('ilan_id')->constrained('ilanlar')->onDelete('cascade');
            $table->foreignId('feature_id')->constrained('features')->onDelete('cascade');
            $table->string('value')->nullable()->comment('Feature value (for checkbox, number, select)');
            $table->timestamps();

            $table->primary(['ilan_id', 'feature_id'], 'ilan_feature_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ilan_feature');
    }
};
