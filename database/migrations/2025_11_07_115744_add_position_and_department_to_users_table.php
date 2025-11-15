<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Context7 Compliance: position and department fields for danisman
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Position (Pozisyon) - Danışman pozisyonu
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position', 100)->nullable()->after('title');
            }
            
            // Department (Departman) - Danışman departmanı
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department', 100)->nullable()->after('position');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'position')) {
                $table->dropColumn('position');
            }
            if (Schema::hasColumn('users', 'department')) {
                $table->dropColumn('department');
            }
        });
    }
};
