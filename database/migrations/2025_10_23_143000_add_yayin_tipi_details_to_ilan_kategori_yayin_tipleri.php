<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Yayın Tipi Detay Alanları Ekleme
     * Context7 Compliant - 2025-10-23
     */
    public function up(): void
    {
        Schema::table('ilan_kategori_yayin_tipleri', function (Blueprint $table) {
            // Açıklama ve detay alanları
            $table->text('aciklama')->nullable()->after('yayin_tipi')
                ->comment('Yayın tipi detaylı açıklaması');
            
            $table->string('icon', 10)->nullable()->after('aciklama')
                ->comment('Emoji icon (💰, 🔑, 📅, etc.)');
            
            $table->boolean('populer')->default(false)->after('icon')
                ->comment('Popüler yayın tipi mi?');
            
            $table->integer('sira')->nullable()->after('populer')
                ->comment('Görüntüleme sırası');
            
            // Index for performance
            $table->index('populer', 'idx_populer');
            $table->index('sira', 'idx_sira');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilan_kategori_yayin_tipleri', function (Blueprint $table) {
            $table->dropIndex('idx_populer');
            $table->dropIndex('idx_sira');
            
            $table->dropColumn([
                'aciklama',
                'icon',
                'populer',
                'sira',
            ]);
        });
    }
};

