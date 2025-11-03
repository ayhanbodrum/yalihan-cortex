<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * CONTEXT7: applies_to field'ı ekle
     * Bu field hangi kategorilere (arsa, konut, ticari, yazlik-kiralama) uygulandığını belirtir
     *
     * Örnek:
     * - 'all' = Tüm kategorilere
     * - 'arsa' = Sadece arsa
     * - 'konut,yazlik-kiralama' = Konut ve yazlık kiralama
     */
    public function up(): void
    {
        Schema::table('feature_categories', function (Blueprint $table) {
            // JSON olarak sakla, birden fazla kategori seçilebilir
            $table->json('applies_to')->nullable()->after('status')
                ->comment('Hangi ilan kategorilerine uygulanır (JSON array)');

            // Index ekle (arama için)
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feature_categories', function (Blueprint $table) {
            $table->dropColumn('applies_to');
        });
    }
};
