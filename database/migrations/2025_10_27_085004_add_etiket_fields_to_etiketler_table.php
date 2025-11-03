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
        Schema::table('etiketler', function (Blueprint $table) {
            $table->string('type')->default('feature')->after('slug');
            $table->string('icon')->nullable()->after('type');
            $table->string('bg_color', 7)->default('#3B82F6')->after('icon');
            $table->string('badge_text')->nullable()->after('bg_color');
            $table->boolean('is_badge')->default(false)->after('badge_text');
            $table->string('target_url')->nullable()->after('is_badge');

            $table->index('type');
            $table->index(['is_badge', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etiketler', function (Blueprint $table) {
            $table->dropIndex(['is_badge', 'status']);
            $table->dropIndex(['type']);
            $table->dropColumn(['type', 'icon', 'bg_color', 'badge_text', 'is_badge', 'target_url']);
        });
    }
};
