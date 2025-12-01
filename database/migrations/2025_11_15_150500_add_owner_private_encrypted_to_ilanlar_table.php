<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            if (! Schema::hasColumn('ilanlar', 'owner_private_encrypted')) {
                $table->text('owner_private_encrypted')->nullable()->after('portal_pricing');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            if (Schema::hasColumn('ilanlar', 'owner_private_encrypted')) {
                $table->dropColumn('owner_private_encrypted');
            }
        });
    }
};
