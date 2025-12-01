<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7: TurkiyeAPI + WikiMapia integration
     * Support for Mahalle, Belde, Köy + Environmental data
     */
    public function up(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Location type (mahalle, belde, koy)
            $table->string('location_type', 20)->nullable()->after('mahalle_id')->comment('Lokasyon tipi: mahalle, belde, koy');

            // TurkiyeAPI location data
            $table->json('location_data')->nullable()->after('location_type')->comment('TurkiyeAPI data: population, postcode, isCoastal, etc.');

            // WikiMapia place ID
            $table->bigInteger('wikimapia_place_id')->nullable()->after('location_data')->comment('WikiMapia place/site ID');

            // Environmental scores (calculated from WikiMapia)
            $table->json('environmental_scores')->nullable()->after('wikimapia_place_id')->comment('Walkability, convenience, family-friendly scores');

            // Nearby places summary (from WikiMapia)
            $table->json('nearby_places')->nullable()->after('environmental_scores')->comment('Yakındaki yerler özeti (market, okul, plaj, etc.)');

            // Index for location_type
            $table->index('location_type');
            $table->index('wikimapia_place_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            $table->dropColumn([
                'location_type',
                'location_data',
                'wikimapia_place_id',
                'environmental_scores',
                'nearby_places',
            ]);
        });
    }
};
