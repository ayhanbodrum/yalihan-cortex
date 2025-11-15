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
            // Yazlık kiralama fiyat alanları (Vacation Rental Pricing)
            $table->decimal('gunluk_fiyat', 10, 2)->nullable()->after('fiyat')->comment('Günlük kiralama fiyatı');
            $table->decimal('haftalik_fiyat', 10, 2)->nullable()->after('gunluk_fiyat')->comment('Haftalık kiralama fiyatı');
            $table->decimal('aylik_fiyat', 10, 2)->nullable()->after('haftalik_fiyat')->comment('Aylık kiralama fiyatı');
            $table->decimal('sezonluk_fiyat', 10, 2)->nullable()->after('aylik_fiyat')->comment('Sezonluk kiralama fiyatı');

            // Konaklama kuralları (Accommodation Rules)
            $table->integer('min_konaklama')->nullable()->after('sezonluk_fiyat')->comment('Minimum konaklama günü');
            $table->integer('max_misafir')->nullable()->after('min_konaklama')->comment('Maksimum misafir sayısı');
            $table->decimal('temizlik_ucreti', 10, 2)->nullable()->after('max_misafir')->comment('Temizlik ücreti');

            // Sezon tarihleri (Season Dates)
            $table->date('sezon_baslangic')->nullable()->after('temizlik_ucreti')->comment('Sezon başlangıç tarihi');
            $table->date('sezon_bitis')->nullable()->after('sezon_baslangic')->comment('Sezon bitiş tarihi');

            // Dahil hizmetler (Included Services) - Boolean
            $table->boolean('elektrik_dahil')->default(false)->after('sezon_bitis')->comment('Elektrik dahil mi?');
            $table->boolean('su_dahil')->default(false)->after('elektrik_dahil')->comment('Su dahil mi?');

            // Havuz özellikleri (Pool Features)
            $table->boolean('havuz')->default(false)->after('su_dahil')->comment('Havuz var mı?');
            $table->boolean('havuz_var')->default(false)->after('havuz')->comment('Havuz var (legacy)');
            $table->string('havuz_turu', 50)->nullable()->after('havuz_var')->comment('Havuz türü: Özel, Ortak, Infinity');
            $table->string('havuz_boyut', 50)->nullable()->after('havuz_turu')->comment('Havuz boyutu (örn: 8x4m)');
            $table->decimal('havuz_derinlik', 5, 2)->nullable()->after('havuz_boyut')->comment('Havuz derinliği (m)');

            // Index'ler
            $table->index('min_konaklama', 'idx_ilanlar_min_konaklama');
            $table->index(['sezon_baslangic', 'sezon_bitis'], 'idx_ilanlar_sezon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            // Index'leri kaldır
            $table->dropIndex('idx_ilanlar_min_konaklama');
            $table->dropIndex('idx_ilanlar_sezon');

            // Kolonları kaldır
            $table->dropColumn([
                'gunluk_fiyat', 'haftalik_fiyat', 'aylik_fiyat', 'sezonluk_fiyat',
                'min_konaklama', 'max_misafir', 'temizlik_ucreti',
                'sezon_baslangic', 'sezon_bitis',
                'elektrik_dahil', 'su_dahil',
                'havuz', 'havuz_var', 'havuz_turu', 'havuz_boyut', 'havuz_derinlik',
            ]);
        });
    }
};
