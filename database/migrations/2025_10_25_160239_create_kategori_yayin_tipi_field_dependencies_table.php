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
        if (Schema::hasTable('kategori_yayin_tipi_field_dependencies')) {
            return; // Tablo zaten var, migration'ı atla
        }

        Schema::create('kategori_yayin_tipi_field_dependencies', function (Blueprint $table) {
            $table->id();

            // 2D Keys
            $table->string('kategori_slug', 100)->comment('Kategori slug: konut, arsa, yazlik, isyeri');
            $table->string('yayin_tipi', 100)->comment('Yayın tipi: Satılık, Kiralık, Sezonluk Kiralık, Devren Satış');

            // Field Info
            $table->string('field_slug', 100)->comment('Field slug: ada_no, gunluk_fiyat, oda_sayisi');
            $table->string('field_name', 255)->comment('Field görünen adı: Ada No, Günlük Fiyat, Oda Sayısı');
            $table->enum('field_type', ['text', 'number', 'boolean', 'select', 'textarea', 'date', 'price'])->comment('Field tipi');
            $table->string('field_category', 50)->comment('Field kategorisi: fiyat, ozellik, dokuман, sezonluk, arsa');
            $table->string('field_icon', 10)->nullable()->comment('Field ikonu emoji');

            // Options (for select fields)
            $table->json('field_options')->nullable()->comment('Select field seçenekleri: {"1+1": "1+1", "2+1": "2+1"}');
            $table->string('field_unit', 20)->nullable()->comment('Field birimi: m², TL, gün');

            // Behavior
            $table->boolean('status')->default(true)->comment('Field aktif mi?');
            $table->boolean('required')->default(false)->comment('Field zorunlu mu?');
            $table->integer('display_order')->default(0)->comment('Sıralama'); // Context7: order → display_order

            // AI Features
            $table->boolean('ai_auto_fill')->default(false)->comment('AI otomatik doldurma');
            $table->boolean('ai_suggestion')->default(false)->comment('AI öneri sistemi');
            $table->boolean('ai_calculation')->default(false)->comment('AI hesaplama');
            $table->string('ai_prompt_key', 255)->nullable()->comment('AI prompt anahtarı');

            // UI Display
            $table->boolean('searchable')->default(false)->comment('Aranabilir mi?');
            $table->boolean('show_in_card')->default(false)->comment('Kart görünümünde göster');

            $table->timestamps();

            // Indexes
            $table->unique(['kategori_slug', 'yayin_tipi', 'field_slug'], 'unique_kategori_yayin_field');
            $table->index(['kategori_slug', 'yayin_tipi'], 'idx_kategori_yayin');
            $table->index('field_slug', 'idx_field_slug');
            $table->index('enabled', 'idx_enabled');
            $table->index('field_category', 'idx_field_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_yayin_tipi_field_dependencies');
    }
};
