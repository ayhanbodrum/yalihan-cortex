<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            if (!Schema::hasColumn('ilanlar', 'price_text')) {
                $table->string('price_text')->nullable()->after('fiyat');
            }

            if (!Schema::hasColumn('ilanlar', 'nearby_places')) {
                $table->json('nearby_places')->nullable()->after('location_data');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            if (Schema::hasColumn('ilanlar', 'price_text')) {
                $table->dropColumn('price_text');
            }

            if (Schema::hasColumn('ilanlar', 'nearby_places')) {
                $table->dropColumn('nearby_places');
            }
        });
    }
};

