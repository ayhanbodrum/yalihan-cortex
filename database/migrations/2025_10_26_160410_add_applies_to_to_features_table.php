<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('features', function (Blueprint $table) {
            $table->string('applies_to')->nullable()->after('feature_category_id');
            $table->integer('display_order')->default(0)->after('applies_to');
        });
    }

    public function down(): void
    {
        Schema::table('features', function (Blueprint $table) {
            $table->dropColumn(['applies_to', 'display_order']);
        });
    }
};
