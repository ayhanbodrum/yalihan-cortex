<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add AI Scoring Fields to Kisiler Table
 *
 * Context7: AI analysis fields for CRM customer intelligence
 * - yatirimci_profili: Investor profile type
 * - satis_potansiyeli: Sales potential score (0-100)
 * - aciliyet_derecesi: Urgency level
 * - karar_verici_mi: Decision maker flag
 * - crm_status: CRM status (separate from status field)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Context7: AI scoring fields for CRM analysis
     */
    public function up(): void
    {
        // ✅ CONTEXT7: Tablo varlık kontrolü
        if (! Schema::hasTable('kisiler')) {
            return;
        }

        Schema::table('kisiler', function (Blueprint $table) {
            // Yatırımcı profili (enum: konservatif, agresif, firsatci, denge, yeni_baslayan)
            $table->string('yatirimci_profili', 50)->nullable()
                ->comment('Yatırımcı profili: konservatif, agresif, firsatci, denge, yeni_baslayan');

            // Satış potansiyeli skoru (0-100)
            $table->tinyInteger('satis_potansiyeli')->nullable()->default(0)
                ->comment('Satış potansiyeli skoru (0-100)');

            // Aciliyet derecesi (enum: yuksek, orta, dusuk)
            $table->string('aciliyet_derecesi', 20)->nullable()
                ->comment('Aciliyet derecesi: yuksek, orta, dusuk');

            // Karar verici mi? (boolean)
            $table->boolean('karar_verici_mi')->default(true)
                ->comment('Karar verici mi yoksa etkilenen mi?');

            // CRM Status (enum: sicak, soguk, takipte, musteri, potansiyel, ilgili, pasif)
            $table->string('crm_status', 20)->nullable()
                ->comment('CRM status: sicak, soguk, takipte, musteri, potansiyel, ilgili, pasif');

            // Indexes for performance
            $table->index('crm_status', 'kisiler_crm_status_index');
            $table->index('satis_potansiyeli', 'kisiler_satis_potansiyeli_index');
            $table->index('yatirimci_profili', 'kisiler_yatirimci_profili_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('kisiler')) {
            return;
        }

        Schema::table('kisiler', function (Blueprint $table) {
            $table->dropIndex('kisiler_crm_status_index');
            $table->dropIndex('kisiler_satis_potansiyeli_index');
            $table->dropIndex('kisiler_yatirimci_profili_index');

            $table->dropColumn([
                'yatirimci_profili',
                'satis_potansiyeli',
                'aciliyet_derecesi',
                'karar_verici_mi',
                'crm_status',
            ]);
        });
    }
};
