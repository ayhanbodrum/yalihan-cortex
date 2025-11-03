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
        Schema::table('ilanlar', function (Blueprint $table) {
            $table->foreignId('danisman_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            $table->index('danisman_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            $table->dropForeign(['danisman_id']);
            $table->dropIndex(['danisman_id']);
            $table->dropColumn('danisman_id');
        });
    }
};
