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
        Schema::table('site_apartmanlar', function (Blueprint $table) {
            $table->enum('tip', ['site', 'apartman'])->default('site')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_apartmanlar', function (Blueprint $table) {
            $table->dropColumn('tip');
        });
    }
};
