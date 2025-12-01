<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 🎯 2D MATRIX: Kategori × Yayın Tipi → Field Dependency
     * 🤖 AI-POWERED: AI ile field suggestion ve auto-fill
     */
    public function up(): void
    {
        Schema::create('kategori_yayin_tipi_field_dependencies', function (Blueprint $table) {
            $table->id();

            // ═══════════════════════════════════════════════════════════
            // 🎯 2D MATRIX KEYS
            // ═══════════════════════════════════════════════════════════
            $table->string('kategori_slug', 100)->comment('Kategori slug: konut, arsa, yazlik, isyeri');
            $table->string('yayin_tipi', 100)->comment('Yayın tipi: Satılık, Kiralık, Sezonluk Kiralık, Devren Satış');

            // ═══════════════════════════════════════════════════════════
            // 📝 FIELD INFORMATION
            // ═══════════════════════════════════════════════════════════
            $table->string('field_slug', 100)->comment('Field slug: ada_no, gunluk_fiyat, oda_sayisi');
            $table->string('field_name', 255)->comment('Field görünen adı: Ada No, Günlük Fiyat');
            $table->enum('field_type', [
                'text',      // Serbest metin
                'number',    // Sayısal
                'boolean',   // Evet/Hayır
                'select',    // Seçim listesi
                'textarea',  // Uzun metin
                'date',      // Tarih
                'price',     // Fiyat (özel)
                'location',   // Konum (özel)
            ])->comment('Field tipi');

            $table->string('field_category', 50)->comment('Field kategorisi: fiyat, ozellik, dokuман, sezonluk, arsa');

            // ═══════════════════════════════════════════════════════════
            // 🎨 FIELD OPTIONS & METADATA
            // ═══════════════════════════════════════════════════════════
            $table->json('field_options')->nullable()->comment('Select field için seçenekler: {"1+1":"1+1","2+1":"2+1"}');
            $table->string('field_unit', 20)->nullable()->comment('Birim: m², TL, Gün, %');
            $table->string('field_placeholder', 255)->nullable()->comment('Placeholder metin');
            $table->string('field_help', 500)->nullable()->comment('Yardım metni');
            $table->string('field_icon', 50)->nullable()->comment('Icon: 🏠, 📊, 💰');

            // ═══════════════════════════════════════════════════════════
            // ⚙️ BEHAVIOR SETTINGS
            // ═══════════════════════════════════════════════════════════
            $table->tinyInteger('status')->default(1)->comment('0=disabled, 1=enabled (Context7: enabled → status)');
            $table->tinyInteger('required')->default(0)->comment('0=optional, 1=required (Context7 boolean)');
            $table->tinyInteger('searchable')->default(0)->comment('0=not searchable, 1=searchable');
            $table->tinyInteger('show_in_card')->default(0)->comment('0=hide in card, 1=show in card');
            $table->integer('display_order')->default(0)->comment('Sıralama'); // Context7: order → display_order

            // ═══════════════════════════════════════════════════════════
            // 🤖 AI INTEGRATION
            // ═══════════════════════════════════════════════════════════
            $table->tinyInteger('ai_suggestion')->default(0)->comment('AI ile öneri yapılsın mı?');
            $table->tinyInteger('ai_auto_fill')->default(0)->comment('AI ile otomatik doldurulsun mu?');
            $table->string('ai_prompt_key', 100)->nullable()->comment('AI prompt dosyası key: arsa-ada-no-suggest');
            $table->json('ai_context')->nullable()->comment('AI için context bilgileri');

            // ═══════════════════════════════════════════════════════════
            // 📊 VALIDATION RULES
            // ═══════════════════════════════════════════════════════════
            $table->string('validation_min', 50)->nullable()->comment('Min değer/uzunluk');
            $table->string('validation_max', 50)->nullable()->comment('Max değer/uzunluk');
            $table->json('validation_rules')->nullable()->comment('Laravel validation rules');

            // ═══════════════════════════════════════════════════════════
            // 🕒 TIMESTAMPS
            // ═══════════════════════════════════════════════════════════
            $table->timestamps();

            // ═══════════════════════════════════════════════════════════
            // 🔑 INDEXES & CONSTRAINTS
            // ═══════════════════════════════════════════════════════════
            $table->unique(
                ['kategori_slug', 'yayin_tipi', 'field_slug'],
                'unique_kategori_yayin_field'
            );
            $table->index(['kategori_slug', 'yayin_tipi'], 'idx_kategori_yayin');
            $table->index('field_slug', 'idx_field_slug');
            $table->index('status', 'idx_status'); // Context7: enabled → status
            $table->index('ai_suggestion', 'idx_ai_suggestion');
            $table->index(['field_category', 'display_order'], 'idx_category_display_order'); // Context7: order → display_order
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
