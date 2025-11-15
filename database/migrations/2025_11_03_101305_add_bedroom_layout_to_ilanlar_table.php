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
            // Yatak odası detayları (TatildeKirala/Airbnb "Nerede Uyuyacaksınız" özelliği)
            $table->json('bedroom_layout')->nullable();
            $table->text('sleeping_arrangement_notes')->nullable();

            // Örnek bedroom_layout structure:
            // {
            //   "bedrooms": [
            //     {"room": 1, "name": "Ana Yatak Odası", "bed_type": "double", "bed_count": 1, "capacity": 2, "ensuite": true},
            //     {"room": 2, "name": "Misafir Odası", "bed_type": "single", "bed_count": 2, "capacity": 2, "ensuite": false}
            //   ],
            //   "extra_sleeping": [
            //     {"location": "Oturma Odası", "bed_type": "sofa_bed", "count": 1, "capacity": 1}
            //   ],
            //   "total_capacity": 5
            // }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            $table->dropColumn(['bedroom_layout', 'sleeping_arrangement_notes']);
        });
    }
};
