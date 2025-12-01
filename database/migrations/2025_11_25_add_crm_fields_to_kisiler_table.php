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
        Schema::table('kisiler', function (Blueprint $table) {
            // Segmentasyon
            $table->string('segment')->default('potansiyel')->after('status')
                ->comment('potansiyel, aktif, eski, vip');

            // Lead scoring
            $table->integer('skor')->default(0)->after('segment')
                ->comment('0-100 arası lead score');

            // Pipeline stage
            $table->tinyInteger('pipeline_stage')->default(1)->after('skor')
                ->comment('1: yeni_lead, 2: iletisim_kuruldu, 3: teklif_verildi, 4: gorusme_yapildi, 5: kazanildi, 0: kaybedildi');

            // Son etkileşim
            $table->timestamp('son_etkilesim')->nullable()->after('pipeline_stage');

            // Referans
            $table->foreignId('referans_kisi_id')->nullable()->after('son_etkilesim')
                ->constrained('kisiler')->onDelete('set null');
            $table->text('referans_notlari')->nullable()->after('referans_kisi_id');

            // Lead source
            $table->string('lead_source')->nullable()->after('referans_notlari')
                ->comment('website, telefon, referans, sosyal_medya, ofis_ziyareti, diger');

            // Indexes
            $table->index('segment');
            $table->index('pipeline_stage');
            $table->index('son_etkilesim');
            $table->index('lead_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kisiler', function (Blueprint $table) {
            $table->dropIndex(['segment']);
            $table->dropIndex(['pipeline_stage']);
            $table->dropIndex(['son_etkilesim']);
            $table->dropIndex(['lead_source']);

            $table->dropForeign(['referans_kisi_id']);
            $table->dropColumn([
                'segment',
                'skor',
                'pipeline_stage',
                'son_etkilesim',
                'referans_kisi_id',
                'referans_notlari',
                'lead_source',
            ]);
        });
    }
};
