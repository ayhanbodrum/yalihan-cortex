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
        Schema::create('site_ozellikleri', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Özellik adı (örn: Güvenlik, Otopark)');
            $table->string('slug', 100)->unique()->comment('URL-friendly slug');
            $table->string('type', 50)->default('amenity')->comment('Özellik tipi: amenity, security, facility');
            $table->text('description')->nullable()->comment('Özellik açıklaması');
            $table->integer('order')->default(0)->comment('Sıralama');
            $table->boolean('status')->default(true)->comment('Context7: Aktif/Pasif');
            $table->timestamps();
            
            // Indexes
            $table->index('type', 'idx_site_ozellikleri_type');
            $table->index('status', 'idx_site_ozellikleri_status');
            $table->index('order', 'idx_site_ozellikleri_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_ozellikleri');
    }
};
