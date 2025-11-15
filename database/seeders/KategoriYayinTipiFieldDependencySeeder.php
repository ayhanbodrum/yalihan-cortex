<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KategoriYayinTipiFieldDependencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * 🎯 2D MATRIX: Kategori × Yayın Tipi → Field Dependencies
     * 🤖 AI-POWERED: AI suggestion ve auto-fill desteği
     */
    public function run(): void
    {
        Log::info('🎯 2D Matrix Field Dependencies oluşturuluyor...');

        // Önce tabloyu temizle
        DB::table('kategori_yayin_tipi_field_dependencies')->truncate();

        // ═══════════════════════════════════════════════════════════
        // 1️⃣  KONUT KATEGORİSİ
        // ═══════════════════════════════════════════════════════════
        $this->seedKonut();

        // ═══════════════════════════════════════════════════════════
        // 2️⃣  ARSA KATEGORİSİ
        // ═══════════════════════════════════════════════════════════
        $this->seedArsa();

        // ═══════════════════════════════════════════════════════════
        // 3️⃣  YAZLIK KATEGORİSİ
        // ═══════════════════════════════════════════════════════════
        $this->seedYazlik();

        // ═══════════════════════════════════════════════════════════
        // 4️⃣  İŞYERİ KATEGORİSİ
        // ═══════════════════════════════════════════════════════════
        $this->seedIsyeri();

        Log::info('🎉 2D Matrix Field Dependencies başarıyla oluşturuldu!');
    }

    // ═══════════════════════════════════════════════════════════
    // 🏠 KONUT KATEGORİSİ
    // ═══════════════════════════════════════════════════════════
    private function seedKonut(): void
    {
        $fields = [];
        $order = 1;

        // ─────────────────────────────────────────────────────────
        // KONUT × SATILIK
        // ─────────────────────────────────────────────────────────
        $fields[] = $this->createField('konut', 'Satılık', 'satis_fiyati', 'Satış Fiyatı', 'price', 'fiyat', null, 'TL', $order++, true, true, true, '💰');
        $fields[] = $this->createField('konut', 'Satılık', 'm2_fiyati', 'm² Fiyatı', 'number', 'fiyat', null, 'TL/m²', $order++, false, false, true, '📐');
        $fields[] = $this->createField('konut', 'Satılık', 'tapu_tipi', 'Tapu Tipi', 'select', 'dokuман', json_encode(['Kat Mülkiyeti' => 'Kat Mülkiyeti', 'Kat İrtifakı' => 'Kat İrtifakı', 'Arsa Tapusu' => 'Arsa Tapusu']), null, $order++, false, true, false, '📄');
        $fields[] = $this->createField('konut', 'Satılık', 'krediye_uygun', 'Krediye Uygun', 'boolean', 'ozellik', null, null, $order++, false, true, true, '💳');
        $fields[] = $this->createField('konut', 'Satılık', 'takas', 'Takas', 'boolean', 'ozellik', null, null, $order++, false, false, true, '🔄');

        // 🏗️ ARSA FIELD'LARI (Konut için ekleniyor!)
        $fields[] = $this->createField('konut', 'Satılık', 'ada_no', 'Ada No', 'text', 'arsa', null, null, $order++, false, true, true, '🏘️', true); // AI suggestion (TKGM)
        $fields[] = $this->createField('konut', 'Satılık', 'parsel_no', 'Parsel No', 'text', 'arsa', null, null, $order++, false, true, true, '📍', true); // AI suggestion (TKGM)
        $fields[] = $this->createField('konut', 'Satılık', 'imar_statusu', 'İmar Durumu', 'select', 'arsa', json_encode(['İmarlı' => 'İmarlı', 'İmarsız' => 'İmarsız', 'Tarla' => 'Tarla', 'Konut' => 'Konut İmarlı', 'Ticari' => 'Ticari İmarlı']), null, $order++, false, true, true, '🏗️', true); // AI suggestion
        $fields[] = $this->createField('konut', 'Satılık', 'kaks', 'KAKS', 'number', 'arsa', null, '%', $order++, false, false, true, '📊', true); // AI suggestion
        $fields[] = $this->createField('konut', 'Satılık', 'taks', 'TAKS', 'number', 'arsa', null, '%', $order++, false, false, true, '📊', true); // AI suggestion
        $fields[] = $this->createField('konut', 'Satılık', 'gabari', 'Gabari', 'number', 'arsa', null, 'm', $order++, false, false, true, '📏', true); // AI suggestion
        $fields[] = $this->createField('konut', 'Satılık', 'alan_m2', 'Arsa Metrekare', 'number', 'arsa', null, 'm²', $order++, false, true, true, '📐');

        // ─────────────────────────────────────────────────────────
        // KONUT × KİRALIK
        // ─────────────────────────────────────────────────────────
        $order = 1;
        $fields[] = $this->createField('konut', 'Kiralık', 'kira_bedeli', 'Kira Bedeli', 'price', 'fiyat', null, 'TL/Ay', $order++, true, true, true, '🏠');
        $fields[] = $this->createField('konut', 'Kiralık', 'depozito', 'Depozito', 'number', 'fiyat', null, 'TL', $order++, false, true, false, '💰');
        $fields[] = $this->createField('konut', 'Kiralık', 'aidat', 'Aidat', 'number', 'fiyat', null, 'TL/Ay', $order++, false, true, false, '🏢');
        $fields[] = $this->createField('konut', 'Kiralık', 'esyali', 'Eşyalı', 'select', 'ozellik', json_encode(['Hayır' => 'Hayır', 'Kısmen Eşyalı' => 'Kısmen Eşyalı', 'Evet' => 'Evet']), null, $order++, false, true, true, '🛋️', true); // AI suggestion
        $fields[] = $this->createField('konut', 'Kiralık', 'pet_friendly', 'Evcil Hayvan', 'boolean', 'ozellik', null, null, $order++, false, false, true, '🐕');

        // 🏗️ ARSA FIELD'LARI (Kiralık için de!)
        $fields[] = $this->createField('konut', 'Kiralık', 'ada_no', 'Ada No', 'text', 'arsa', null, null, $order++, false, false, false, '🏘️');
        $fields[] = $this->createField('konut', 'Kiralık', 'parsel_no', 'Parsel No', 'text', 'arsa', null, null, $order++, false, false, false, '📍');
        $fields[] = $this->createField('konut', 'Kiralık', 'imar_statusu', 'İmar Durumu', 'select', 'arsa', json_encode(['İmarlı' => 'İmarlı', 'İmarsız' => 'İmarsız', 'Konut' => 'Konut İmarlı']), null, $order++, false, false, false, '🏗️');

        // ─────────────────────────────────────────────────────────
        // KONUT × SEZONLUK KİRALIK
        // ─────────────────────────────────────────────────────────
        $order = 1;
        $fields[] = $this->createField('konut', 'Sezonluk Kiralık', 'gunluk_fiyat', 'Günlük Fiyat', 'price', 'sezonluk', null, 'TL/Gün', $order++, true, true, true, '🌞');
        $fields[] = $this->createField('konut', 'Sezonluk Kiralık', 'haftalik_fiyat', 'Haftalık Fiyat', 'price', 'sezonluk', null, 'TL/Hafta', $order++, false, true, false, '📅');
        $fields[] = $this->createField('konut', 'Sezonluk Kiralık', 'minimum_konaklama', 'Minimum Konaklama', 'number', 'sezonluk', null, 'Gün', $order++, false, true, false, '📆', true); // AI suggestion
        $fields[] = $this->createField('konut', 'Sezonluk Kiralık', 'check_in', 'Check-in Saati', 'select', 'sezonluk', json_encode(['14:00' => '14:00', '15:00' => '15:00', '16:00' => '16:00']), null, $order++, false, false, false, '⏰');
        $fields[] = $this->createField('konut', 'Sezonluk Kiralık', 'check_out', 'Check-out Saati', 'select', 'sezonluk', json_encode(['10:00' => '10:00', '11:00' => '11:00', '12:00' => '12:00']), null, $order++, false, false, false, '⏰');

        DB::table('kategori_yayin_tipi_field_dependencies')->insert($fields);
        Log::info('  ✅ Konut kategorisi fieldlari eklendi (' . count($fields) . ' adet)');
    }

    // ═══════════════════════════════════════════════════════════
    // 🏗️ ARSA KATEGORİSİ
    // ═══════════════════════════════════════════════════════════
    private function seedArsa(): void
    {
        $fields = [];
        $order = 1;

        // ─────────────────────────────────────────────────────────
        // ARSA × SATILIK (En önemli!)
        // ─────────────────────────────────────────────────────────
        $fields[] = $this->createField('arsa', 'Satılık', 'satis_fiyati', 'Satış Fiyatı', 'price', 'fiyat', null, 'TL', $order++, true, true, true, '💰');
        $fields[] = $this->createField('arsa', 'Satılık', 'm2_fiyati', 'm² Fiyatı', 'number', 'fiyat', null, 'TL/m²', $order++, false, false, true, '📐', true); // AI auto-calculate

        // Arsa Özel Field'lar
        $fields[] = $this->createField('arsa', 'Satılık', 'ada_no', 'Ada No', 'text', 'arsa', null, null, $order++, false, true, true, '🏘️', true); // AI suggestion (TKGM)
        $fields[] = $this->createField('arsa', 'Satılık', 'parsel_no', 'Parsel No', 'text', 'arsa', null, null, $order++, false, true, true, '📍', true); // AI suggestion (TKGM)
        $fields[] = $this->createField('arsa', 'Satılık', 'imar_statusu', 'İmar Durumu', 'select', 'arsa', json_encode(['İmarlı' => 'İmarlı', 'İmarsız' => 'İmarsız', 'Tarla' => 'Tarla', 'Konut' => 'Konut İmarlı', 'Ticari' => 'Ticari İmarlı']), null, $order++, false, true, true, '🏗️', true); // AI suggestion
        $fields[] = $this->createField('arsa', 'Satılık', 'kaks', 'KAKS', 'number', 'arsa', null, '%', $order++, false, false, true, '📊', true); // AI suggestion
        $fields[] = $this->createField('arsa', 'Satılık', 'taks', 'TAKS', 'number', 'arsa', null, '%', $order++, false, false, true, '📊', true); // AI suggestion
        $fields[] = $this->createField('arsa', 'Satılık', 'gabari', 'Gabari', 'number', 'arsa', null, 'm', $order++, false, false, true, '📏', true); // AI suggestion
        $fields[] = $this->createField('arsa', 'Satılık', 'alan_m2', 'Arsa Metrekare', 'number', 'arsa', null, 'm²', $order++, false, true, true, '📐');
        $fields[] = $this->createField('arsa', 'Satılık', 'ifrazsiz', 'İfrazsız Satılık', 'boolean', 'arsa', null, null, $order++, false, false, true, '📋');
        $fields[] = $this->createField('arsa', 'Satılık', 'kat_karsiligi', 'Kat Karşılığı', 'boolean', 'arsa', null, null, $order++, false, false, true, '🏢');

        // ─────────────────────────────────────────────────────────
        // ARSA × KİRALIK (Nadiren kullanılır)
        // ─────────────────────────────────────────────────────────
        $order = 1;
        $fields[] = $this->createField('arsa', 'Kiralık', 'kira_bedeli', 'Kira Bedeli', 'price', 'fiyat', null, 'TL/Ay', $order++, true, true, true, '🏠');
        $fields[] = $this->createField('arsa', 'Kiralık', 'ada_no', 'Ada No', 'text', 'arsa', null, null, $order++, false, true, true, '🏘️', true); // AI suggestion
        $fields[] = $this->createField('arsa', 'Kiralık', 'parsel_no', 'Parsel No', 'text', 'arsa', null, null, $order++, false, true, true, '📍', true); // AI suggestion
        $fields[] = $this->createField('arsa', 'Kiralık', 'alan_m2', 'Arsa Metrekare', 'number', 'arsa', null, 'm²', $order++, false, true, true, '📐');

        DB::table('kategori_yayin_tipi_field_dependencies')->insert($fields);
        Log::info('  ✅ Arsa kategorisi fieldlari eklendi (' . count($fields) . ' adet)');
    }

    // ═══════════════════════════════════════════════════════════
    // 🌴 YAZLIK KATEGORİSİ
    // ═══════════════════════════════════════════════════════════
    private function seedYazlik(): void
    {
        $fields = [];
        $order = 1;

        // ─────────────────────────────────────────────────────────
        // YAZLIK × SATILIK
        // ─────────────────────────────────────────────────────────
        $fields[] = $this->createField('yazlik', 'Satılık', 'satis_fiyati', 'Satış Fiyatı', 'price', 'fiyat', null, 'TL', $order++, true, true, true, '💰');
        $fields[] = $this->createField('yazlik', 'Satılık', 'havuz', 'Havuz', 'boolean', 'ozellik', null, null, $order++, false, false, true, '🏊', true); // AI suggestion
        $fields[] = $this->createField('yazlik', 'Satılık', 'denize_uzaklik', 'Denize Uzaklık', 'number', 'ozellik', null, 'km', $order++, false, false, true, '🌊', true); // AI suggestion

        // ─────────────────────────────────────────────────────────
        // YAZLIK × KİRALIK
        // ─────────────────────────────────────────────────────────
        $order = 1;
        $fields[] = $this->createField('yazlik', 'Kiralık', 'kira_bedeli', 'Kira Bedeli', 'price', 'fiyat', null, 'TL/Ay', $order++, true, true, true, '🏠');
        $fields[] = $this->createField('yazlik', 'Kiralık', 'depozito', 'Depozito', 'number', 'fiyat', null, 'TL', $order++, false, true, false, '💰');
        $fields[] = $this->createField('yazlik', 'Kiralık', 'havuz', 'Havuz', 'boolean', 'ozellik', null, null, $order++, false, false, true, '🏊');
        $fields[] = $this->createField('yazlik', 'Kiralık', 'denize_uzaklik', 'Denize Uzaklık', 'number', 'ozellik', null, 'km', $order++, false, false, true, '🌊');

        // ─────────────────────────────────────────────────────────
        // YAZLIK × SEZONLUK KİRALIK (En zengin!)
        // ─────────────────────────────────────────────────────────
        $order = 1;

        // Sezonluk Fiyatlar
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'gunluk_fiyat', 'Günlük Fiyat', 'price', 'sezonluk', null, 'TL/Gün', $order++, true, true, true, '🌞', true); // AI auto-fill
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'haftalik_fiyat', 'Haftalık Fiyat', 'price', 'sezonluk', null, 'TL/Hafta', $order++, false, true, false, '📅', true); // AI auto-calculate
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'aylik_fiyat', 'Aylık Fiyat', 'price', 'sezonluk', null, 'TL/Ay', $order++, false, false, false, '📆', true); // AI auto-calculate

        // Sezon Fiyatları (AI-powered!)
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'yaz_sezonu_fiyat', 'Yaz Sezonu Fiyatı', 'price', 'sezonluk', null, 'TL/Gün', $order++, false, true, false, '☀️', true); // AI suggestion (market analysis)
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'ara_sezon_fiyat', 'Ara Sezon Fiyatı', 'price', 'sezonluk', null, 'TL/Gün', $order++, false, false, false, '🍂', true); // AI auto-calculate (-%20-30)
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'kis_sezonu_fiyat', 'Kış Sezonu Fiyatı', 'price', 'sezonluk', null, 'TL/Gün', $order++, false, false, false, '❄️', true); // AI auto-calculate (-%40-50)

        // Sezonluk Bilgileri
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'minimum_konaklama', 'Minimum Konaklama', 'number', 'sezonluk', null, 'Gün', $order++, false, true, false, '📆', true); // AI suggestion
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'maksimum_misafir', 'Maksimum Misafir', 'number', 'sezonluk', null, 'Kişi', $order++, false, false, true, '👥', true); // AI suggestion (based on m2)
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'check_in', 'Check-in Saati', 'select', 'sezonluk', json_encode(['14:00' => '14:00', '15:00' => '15:00', '16:00' => '16:00']), null, $order++, false, false, false, '⏰');
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'check_out', 'Check-out Saati', 'select', 'sezonluk', json_encode(['10:00' => '10:00', '11:00' => '11:00', '12:00' => '12:00']), null, $order++, false, false, false, '⏰');

        // Yazlık Özellikleri
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'havuz', 'Havuz', 'boolean', 'ozellik', null, null, $order++, false, false, true, '🏊');
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'denize_uzaklik', 'Denize Uzaklık', 'number', 'ozellik', null, 'km', $order++, false, false, true, '🌊', true); // AI suggestion (maps)
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'pet_friendly', 'Evcil Hayvan', 'boolean', 'ozellik', null, null, $order++, false, false, true, '🐕');
        $fields[] = $this->createField('yazlik', 'Sezonluk Kiralık', 'esyali', 'Eşyalı', 'select', 'ozellik', json_encode(['Hayır' => 'Hayır', 'Kısmen' => 'Kısmen', 'Tam Eşyalı' => 'Tam Eşyalı']), null, $order++, false, true, true, '🛋️');

        DB::table('kategori_yayin_tipi_field_dependencies')->insert($fields);
        Log::info('  ✅ Yazlik kategorisi fieldlari eklendi (' . count($fields) . ' adet)');
    }

    // ═══════════════════════════════════════════════════════════
    // 🎯 HELPER: Create Field
    // ═══════════════════════════════════════════════════════════
    private function createField(
        string $kategoriSlug,
        string $yayinTipi,
        string $fieldSlug,
        string $fieldName,
        string $fieldType,
        string $fieldCategory,
        ?string $fieldOptions,
        ?string $fieldUnit,
        int $order,
        bool $required,
        bool $searchable,
        bool $showInCard,
        string $icon,
        bool $aiSuggestion = false
    ): array {
        return [
            'kategori_slug' => $kategoriSlug,
            'yayin_tipi' => $yayinTipi,
            'field_slug' => $fieldSlug,
            'field_name' => $fieldName,
            'field_type' => $fieldType,
            'field_category' => $fieldCategory,
            'field_options' => $fieldOptions,
            'field_unit' => $fieldUnit,
            'field_icon' => $icon,
            'status' => 1, // ✅ Context7: enabled → status
            'required' => $required ? 1 : 0,
            'searchable' => $searchable ? 1 : 0,
            'show_in_card' => $showInCard ? 1 : 0,
            'display_order' => $order,
            'ai_suggestion' => $aiSuggestion ? 1 : 0,
            'ai_auto_fill' => $aiSuggestion ? 1 : 0, // AI suggestion varsa auto-fill da aktif
            'ai_prompt_key' => $aiSuggestion ? "{$kategoriSlug}-{$fieldSlug}-suggest" : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // 🏢 İŞYERİ KATEGORİSİ
    // ═══════════════════════════════════════════════════════════
    private function seedIsyeri(): void
    {
        $fields = [];
        $order = 1;

        // ─────────────────────────────────────────────────────────
        // 💰 FİYAT ALANLARI
        // ─────────────────────────────────────────────────────────
        $fields[] = $this->createField('isyeri', 'Satılık', 'kira_bedeli', 'Kira Bedeli', 'number', 'fiyat', null, '💰', $order++, true, true, true, '💰', true);
        $fields[] = $this->createField('isyeri', 'Kiralık', 'kira_bedeli', 'Kira Bedeli', 'number', 'fiyat', null, '💰', $order++, true, true, true, '💰', true);
        $fields[] = $this->createField('isyeri', 'Satılık', 'satis_fiyati', 'Satış Fiyatı', 'number', 'fiyat', null, '💰', $order++, true, true, true, '💰', true);
        $fields[] = $this->createField('isyeri', 'Kiralık', 'depozito', 'Depozito', 'number', 'fiyat', null, '💰', $order++, false, true, true, '💰', true);
        $fields[] = $this->createField('isyeri', 'Kiralık', 'aidat', 'Aidat', 'number', 'fiyat', null, '💰', $order++, false, true, true, '💰', true);

        // ─────────────────────────────────────────────────────────
        // 🏢 İŞYERİ ÖZELLİKLERİ
        // ─────────────────────────────────────────────────────────
        $fields[] = $this->createField('isyeri', 'Satılık', 'metrekare', 'Metrekare', 'number', 'ozellik', null, 'm²', $order++, true, true, true, '📐', true);
        $fields[] = $this->createField('isyeri', 'Kiralık', 'metrekare', 'Metrekare', 'number', 'ozellik', null, 'm²', $order++, true, true, true, '📐', true);
        $fields[] = $this->createField('isyeri', 'Satılık', 'kat_sayisi', 'Kat Sayısı', 'number', 'ozellik', null, null, $order++, false, true, true, '🏢', true);
        $fields[] = $this->createField('isyeri', 'Kiralık', 'kat_sayisi', 'Kat Sayısı', 'number', 'ozellik', null, null, $order++, false, true, true, '🏢', true);
        $fields[] = $this->createField('isyeri', 'Satılık', 'oda_sayisi', 'Oda Sayısı', 'number', 'ozellik', null, null, $order++, false, true, true, '🏢', true);
        $fields[] = $this->createField('isyeri', 'Kiralık', 'oda_sayisi', 'Oda Sayısı', 'number', 'ozellik', null, null, $order++, false, true, true, '🏢', true);

        // ─────────────────────────────────────────────────────────
        // 🚗 OTOPARK VE ULAŞIM
        // ─────────────────────────────────────────────────────────
        $fields[] = $this->createField('isyeri', 'Satılık', 'otopark', 'Otopark', 'boolean', 'ozellik', null, null, $order++, false, true, true, '🚗', true);
        $fields[] = $this->createField('isyeri', 'Kiralık', 'otopark', 'Otopark', 'boolean', 'ozellik', null, null, $order++, false, true, true, '🚗', true);
        $fields[] = $this->createField('isyeri', 'Satılık', 'asansor', 'Asansör', 'boolean', 'ozellik', null, null, $order++, false, true, true, '🛗', true);
        $fields[] = $this->createField('isyeri', 'Kiralık', 'asansor', 'Asansör', 'boolean', 'ozellik', null, null, $order++, false, true, true, '🛗', true);

        // ─────────────────────────────────────────────────────────
        // 📋 GENEL BİLGİLER
        // ─────────────────────────────────────────────────────────
        $fields[] = $this->createField('isyeri', 'Satılık', 'aciklama', 'Açıklama', 'textarea', 'ozellik', null, null, $order++, false, true, true, '📝', true);
        $fields[] = $this->createField('isyeri', 'Kiralık', 'aciklama', 'Açıklama', 'textarea', 'ozellik', null, null, $order++, false, true, true, '📝', true);

        // Veritabanına kaydet
        DB::table('kategori_yayin_tipi_field_dependencies')->insert($fields);
        Log::info("🏢 İşyeri kategorisi için " . count($fields) . " field dependency oluşturuldu");
    }
}
