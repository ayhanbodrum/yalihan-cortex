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
        Schema::table('feature_categories', function (Blueprint $table) {
            $table->string('applies_to')->nullable()->after('description')
                ->comment('Emlak türleri: konut, arsa, yazlik, isyeri (virgülle ayrılmış)');
            $table->integer('display_order')->default(0)->after('applies_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feature_categories', function (Blueprint $table) {
            $table->dropColumn(['applies_to', 'display_order']);
        });
    }
};
