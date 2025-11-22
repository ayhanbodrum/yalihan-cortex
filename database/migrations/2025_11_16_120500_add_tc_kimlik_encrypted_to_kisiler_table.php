<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kisiler', function (Blueprint $table) {
            if (!Schema::hasColumn('kisiler', 'tc_kimlik_encrypted')) {
                $table->text('tc_kimlik_encrypted')->nullable()->after('tc_kimlik');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kisiler', function (Blueprint $table) {
            if (Schema::hasColumn('kisiler', 'tc_kimlik_encrypted')) {
                $table->dropColumn('tc_kimlik_encrypted');
            }
        });
    }
};