<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Context7 Compliance: Rename site_adi to name
     */
    public function up(): void
    {
        Schema::table('site_apartmanlar', function (Blueprint $table) {
            // ✅ Context7: site_adi → name
            if (Schema::hasColumn('site_apartmanlar', 'site_adi')) {
                $table->renameColumn('site_adi', 'name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_apartmanlar', function (Blueprint $table) {
            // Rollback: name → site_adi
            if (Schema::hasColumn('site_apartmanlar', 'name')) {
                $table->renameColumn('name', 'site_adi');
            }
        });
    }
};
