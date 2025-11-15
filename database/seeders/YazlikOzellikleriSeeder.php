<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OzellikKategori;
use App\Models\Ozellik;

class YazlikOzellikleriSeeder extends Seeder
{
    /**
     * Yazlık Kiralık Özellikleri - Kapsamlı Tanımlamalar
     * Context7 Compliant - 2025-10-23
     *
     * Airbnb + Booking.com + VRBO standartlarında
     */
    public function run(): void
    {
        $this->command->info("🏖️ Yazlık kiralık özellikleri oluşturuluyor...\n");

        // ✅ Yazlık Özellik Kategorileri (6 ana grup)
        $yazlikKategorileri = [
            [
                'name' => 'Konaklama Bilgileri',
                'slug' => 'konaklama-bilgileri',
                'aciklama' => 'Misafir kapasitesi, konaklama süresi ve check-in/out bilgileri',
                'icon' => 'users',
                'display_order' => 20,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Fiyatlandırma',
                'slug' => 'fiyatlandirma',
                'aciklama' => 'Günlük, haftalık, aylık fiyatlar ve ek ücretler',
                'icon' => 'money-bill-wave',
                'display_order' => 21,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Havuz ve Su Sporları',
                'slug' => 'havuz-su-sporlari',
                'aciklama' => 'Havuz, jakuzi, deniz ve su sporları özellikleri',
                'icon' => 'water',
                'display_order' => 22,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Dış Mekan Özellikleri',
                'slug' => 'dis-mekan',
                'aciklama' => 'Bahçe, teras, barbekü, dış alan özellikleri',
                'icon' => 'tree',
                'display_order' => 23,
                'status' => 'Aktif',
            ],
            [
                'name' => 'İç Mekan Donanımları',
                'slug' => 'ic-mekan-donanimlari',
                'aciklama' => 'Mutfak, banyo, yatak odası ve salon donanımları',
                'icon' => 'couch',
                'display_order' => 24,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Konfor ve Eğlence',
                'slug' => 'konfor-eglence',
                'aciklama' => 'Klima, ısıtma, TV, internet ve eğlence sistemleri',
                'icon' => 'tv',
                'display_order' => 25,
                'status' => 'Aktif',
            ],
        ];

        foreach ($yazlikKategorileri as $kategori) {
            OzellikKategori::updateOrCreate(
                ['slug' => $kategori['slug']],
                $kategori
            );
            $this->command->info("✅ Kategori: {$kategori['name']}");
        }

        // ✅ 1. Konaklama Bilgileri Özellikleri
        $konaklamaKategori = OzellikKategori::where('slug', 'konaklama-bilgileri')->first();

        if ($konaklamaKategori) {
            $konaklamaOzellikleri = [
                [
                    'name' => 'Maksimum Misafir',
                    'slug' => 'maksimum-misafir',
                    'kategori_id' => $konaklamaKategori->id,
                    'veri_tipi' => 'number',
                    'birim' => 'kişi',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Maksimum konaklayabilecek misafir sayısı',
                    'display_order' => 1,
                ],
                [
                    'name' => 'Minimum Konaklama',
                    'slug' => 'minimum-konaklama',
                    'kategori_id' => $konaklamaKategori->id,
                    'veri_tipi' => 'number',
                    'birim' => 'gün',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Minimum konaklama süresi (gün)',
                    'display_order' => 2,
                ],
                [
                    'name' => 'Check-In Saati',
                    'slug' => 'check-in-saati',
                    'kategori_id' => $konaklamaKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['12:00', '13:00', '14:00', '15:00', '16:00', '17:00', 'Esnek']),
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Misafir giriş saati',
                    'display_order' => 3,
                ],
                [
                    'name' => 'Check-Out Saati',
                    'slug' => 'check-out-saati',
                    'kategori_id' => $konaklamaKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['09:00', '10:00', '11:00', '12:00', '13:00', 'Esnek']),
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Misafir çıkış saati',
                    'display_order' => 4,
                ],
                [
                    'name' => 'Rezervasyon Tipi',
                    'slug' => 'rezervasyon-tipi',
                    'kategori_id' => $konaklamaKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Anında Onay', 'Talep Üzerine', 'Manuel Onay']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Rezervasyon onay şekli',
                    'display_order' => 5,
                ],
                [
                    'name' => 'İptal Politikası',
                    'slug' => 'iptal-politikasi',
                    'kategori_id' => $konaklamaKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Esnek (24 saat önce)', 'Orta (7 gün önce)', 'Katı (14 gün önce)', 'İptal Yok']),
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'İptal politikası şartları',
                    'display_order' => 6,
                ],
            ];

            foreach ($konaklamaOzellikleri as $ozellik) {
                Ozellik::updateOrCreate(
                    ['slug' => $ozellik['slug']],
                    $ozellik
                );
            }
            $this->command->info("   → 6 konaklama özelliği eklendi");
        }

        // ✅ 2. Fiyatlandırma Özellikleri
        $fiyatKategori = OzellikKategori::where('slug', 'fiyatlandirma')->first();

        if ($fiyatKategori) {
            $fiyatOzellikleri = [
                [
                    'name' => 'Temizlik Ücreti',
                    'slug' => 'temizlik-ucreti',
                    'kategori_id' => $fiyatKategori->id,
                    'veri_tipi' => 'decimal',
                    'birim' => 'TRY',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Tek seferlik temizlik ücreti',
                    'display_order' => 1,
                ],
                [
                    'name' => 'Depozito',
                    'slug' => 'depozito',
                    'kategori_id' => $fiyatKategori->id,
                    'veri_tipi' => 'decimal',
                    'birim' => 'TRY',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Güvenlik depozitosu (iade edilebilir)',
                    'display_order' => 2,
                ],
                [
                    'name' => 'Ekstra Misafir Ücreti',
                    'slug' => 'ekstra-misafir-ucreti',
                    'kategori_id' => $fiyatKategori->id,
                    'veri_tipi' => 'decimal',
                    'birim' => 'TRY/kişi/gece',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Maksimum kişi sayısı aşıldığında kişi başı ücret',
                    'display_order' => 3,
                ],
                [
                    'name' => 'Hafta Sonu İlavesi',
                    'slug' => 'hafta-sonu-ilavesi',
                    'kategori_id' => $fiyatKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Yok', '%10', '%20', '%30', '%50']),
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Hafta sonu fiyat artışı yüzdesi',
                    'display_order' => 4,
                ],
                [
                    'name' => 'Sezon Fiyatlandırması',
                    'slug' => 'sezon-fiyatlandirmasi',
                    'kategori_id' => $fiyatKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Yaz/Kış/Ara sezon farklı fiyatlandırma',
                    'display_order' => 5,
                ],
            ];

            foreach ($fiyatOzellikleri as $ozellik) {
                Ozellik::updateOrCreate(
                    ['slug' => $ozellik['slug']],
                    $ozellik
                );
            }
            $this->command->info("   → 5 fiyatlandırma özelliği eklendi");
        }

        // ✅ 3. Havuz ve Su Sporları
        $havuzKategori = OzellikKategori::where('slug', 'havuz-su-sporlari')->first();

        if ($havuzKategori) {
            $havuzOzellikleri = [
                [
                    'name' => 'Havuz',
                    'slug' => 'havuz',
                    'kategori_id' => $havuzKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Yok', 'Özel Havuz', 'Ortak Havuz', 'Infinity Pool', 'Çocuk Havuzu']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Havuz tipi ve kullanım şekli',
                    'display_order' => 1,
                ],
                [
                    'name' => 'Havuz Boyutu',
                    'slug' => 'havuz-boyutu',
                    'kategori_id' => $havuzKategori->id,
                    'veri_tipi' => 'text',
                    'birim' => 'metre',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Havuz ölçüleri (örn: 8x4m)',
                    'display_order' => 2,
                ],
                [
                    'name' => 'Havuz Isıtması',
                    'slug' => 'havuz-isitmasi',
                    'kategori_id' => $havuzKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Havuz ısıtma sistemi var mı?',
                    'display_order' => 3,
                ],
                [
                    'name' => 'Jakuzi',
                    'slug' => 'jakuzi',
                    'kategori_id' => $havuzKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Jakuzi var mı?',
                    'display_order' => 4,
                ],
                [
                    'name' => 'Denize Mesafe',
                    'slug' => 'denize-mesafe',
                    'kategori_id' => $havuzKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Deniz Manzaralı', '0-50m', '50-100m', '100-300m', '300-500m', '500m+']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Denize yakınlık',
                    'display_order' => 5,
                ],
                [
                    'name' => 'Plaj',
                    'slug' => 'plaj',
                    'kategori_id' => $havuzKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Yok', 'Özel Plaj', 'Halk Plajı Yakın', 'Plaj Kulübü']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Plaj erişimi',
                    'display_order' => 6,
                ],
                [
                    'name' => 'Su Sporları',
                    'slug' => 'su-sporlari',
                    'kategori_id' => $havuzKategori->id,
                    'veri_tipi' => 'multiselect',
                    'veri_secenekleri' => json_encode(['Dalış', 'Kano', 'Rüzgar Sörfü', 'Jet Ski', 'Yelken', 'Kite Sörf']),
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Mevcut su sporları olanakları',
                    'display_order' => 7,
                ],
            ];

            foreach ($havuzOzellikleri as $ozellik) {
                Ozellik::updateOrCreate(
                    ['slug' => $ozellik['slug']],
                    $ozellik
                );
            }
            $this->command->info("   → 7 havuz/su sporları özelliği eklendi");
        }

        // ✅ 4. Dış Mekan Özellikleri
        $dismekanKategori = OzellikKategori::where('slug', 'dis-mekan')->first();

        if ($dismekanKategori) {
            $dismekanOzellikleri = [
                [
                    'name' => 'Bahçe',
                    'slug' => 'bahce',
                    'kategori_id' => $dismekanKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Yok', 'Küçük Bahçe', 'Geniş Bahçe', 'Peyzaj Bahçe']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Bahçe durumu',
                    'display_order' => 1,
                ],
                [
                    'name' => 'Teras',
                    'slug' => 'teras',
                    'kategori_id' => $dismekanKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Teras var mı?',
                    'display_order' => 2,
                ],
                [
                    'name' => 'Barbekü',
                    'slug' => 'barbeku',
                    'kategori_id' => $dismekanKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Barbekü alanı var mı?',
                    'display_order' => 3,
                ],
                [
                    'name' => 'Dış Mobilya',
                    'slug' => 'dis-mobilya',
                    'kategori_id' => $dismekanKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Dış mekan mobilyası (masa, sandalye, şezlong)',
                    'display_order' => 4,
                ],
                [
                    'name' => 'Çocuk Oyun Alanı',
                    'slug' => 'cocuk-oyun-alani',
                    'kategori_id' => $dismekanKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Çocuklar için oyun alanı',
                    'display_order' => 5,
                ],
                [
                    'name' => 'Manzara',
                    'slug' => 'manzara',
                    'kategori_id' => $dismekanKategori->id,
                    'veri_tipi' => 'multiselect',
                    'veri_secenekleri' => json_encode(['Deniz', 'Dağ', 'Orman', 'Göl', 'Doğa', 'Şehir']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Manzara tipleri',
                    'display_order' => 6,
                ],
            ];

            foreach ($dismekanOzellikleri as $ozellik) {
                Ozellik::updateOrCreate(
                    ['slug' => $ozellik['slug']],
                    $ozellik
                );
            }
            $this->command->info("   → 6 dış mekan özelliği eklendi");
        }

        // ✅ 5. İç Mekan Donanımları
        $icmekanKategori = OzellikKategori::where('slug', 'ic-mekan-donanimlari')->first();

        if ($icmekanKategori) {
            $icmekanOzellikleri = [
                [
                    'name' => 'Mutfak Tipi',
                    'slug' => 'mutfak-tipi',
                    'kategori_id' => $icmekanKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Açık Mutfak', 'Kapalı Mutfak', 'Amerikan Mutfak', 'Mutfak Yok']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Mutfak düzeni',
                    'display_order' => 1,
                ],
                [
                    'name' => 'Bulaşık Makinesi',
                    'slug' => 'bulasik-makinesi',
                    'kategori_id' => $icmekanKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Bulaşık makinesi var mı?',
                    'display_order' => 2,
                ],
                [
                    'name' => 'Çamaşır Makinesi',
                    'slug' => 'camasir-makinesi',
                    'kategori_id' => $icmekanKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Çamaşır makinesi var mı?',
                    'display_order' => 3,
                ],
                [
                    'name' => 'Kurutma Makinesi',
                    'slug' => 'kurutma-makinesi',
                    'kategori_id' => $icmekanKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Kurutma makinesi var mı?',
                    'display_order' => 4,
                ],
                [
                    'name' => 'Fırın',
                    'slug' => 'firin',
                    'kategori_id' => $icmekanKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Fırın var mı?',
                    'display_order' => 5,
                ],
                [
                    'name' => 'Mikrodalga',
                    'slug' => 'mikrodalga',
                    'kategori_id' => $icmekanKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Mikrodalga fırın var mı?',
                    'display_order' => 6,
                ],
                [
                    'name' => 'Buzdolabı',
                    'slug' => 'buzdolabi',
                    'kategori_id' => $icmekanKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Buzdolabı var mı?',
                    'display_order' => 7,
                ],
                [
                    'name' => 'Eşyalı',
                    'slug' => 'esyali',
                    'kategori_id' => $icmekanKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Eşyasız', 'Kısmen Eşyalı', 'Tam Eşyalı', 'Lüks Eşyalı']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Eşya durumu',
                    'display_order' => 8,
                ],
            ];

            foreach ($icmekanOzellikleri as $ozellik) {
                Ozellik::updateOrCreate(
                    ['slug' => $ozellik['slug']],
                    $ozellik
                );
            }
            $this->command->info("   → 8 iç mekan özelliği eklendi");
        }

        // ✅ 6. Konfor ve Eğlence
        $konforKategori = OzellikKategori::where('slug', 'konfor-eglence')->first();

        if ($konforKategori) {
            $konforOzellikleri = [
                [
                    'name' => 'Klima',
                    'slug' => 'klima',
                    'kategori_id' => $konforKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Yok', 'Bazı Odalarda', 'Tüm Odalarda', 'Merkezi Klima']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Klima durumu',
                    'display_order' => 1,
                ],
                [
                    'name' => 'Isıtma',
                    'slug' => 'isitma',
                    'kategori_id' => $konforKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Yok', 'Soba', 'Şömine', 'Merkezi Sistem', 'Kombi']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Isıtma sistemi (kış kiralama için)',
                    'display_order' => 2,
                ],
                [
                    'name' => 'Wi-Fi',
                    'slug' => 'wi-fi',
                    'kategori_id' => $konforKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Ücretsiz Wi-Fi internet',
                    'display_order' => 3,
                ],
                [
                    'name' => 'TV',
                    'slug' => 'tv',
                    'kategori_id' => $konforKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Yok', 'Uydu TV', 'Smart TV', 'Netflix/Amazon Prime']),
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'TV sistemi',
                    'display_order' => 4,
                ],
                [
                    'name' => 'Ses Sistemi',
                    'slug' => 'ses-sistemi',
                    'kategori_id' => $konforKategori->id,
                    'veri_tipi' => 'boolean',
                    'zorunlu' => false,
                    'arama_filtresi' => false,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Ses/müzik sistemi var mı?',
                    'display_order' => 5,
                ],
                [
                    'name' => 'Evcil Hayvan',
                    'slug' => 'evcil-hayvan',
                    'kategori_id' => $konforKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['İzinsiz', 'Küçük Hayvan İzinli', 'Tüm Hayvanlar İzinli', 'Ek Ücretli']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Evcil hayvan politikası',
                    'display_order' => 6,
                ],
                [
                    'name' => 'Güvenlik',
                    'slug' => 'guvenlik',
                    'kategori_id' => $konforKategori->id,
                    'veri_tipi' => 'multiselect',
                    'veri_secenekleri' => json_encode(['24 Saat Güvenlik', 'Kamera Sistemi', 'Alarm', 'Kapıcı', 'Site İçi']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => false,
                    'aciklama' => 'Güvenlik önlemleri',
                    'display_order' => 7,
                ],
                [
                    'name' => 'Otopark',
                    'slug' => 'otopark',
                    'kategori_id' => $konforKategori->id,
                    'veri_tipi' => 'select',
                    'veri_secenekleri' => json_encode(['Yok', 'Açık Otopark', 'Kapalı Otopark', 'Özel Garaj']),
                    'zorunlu' => false,
                    'arama_filtresi' => true,
                    'ilan_kartinda_goster' => true,
                    'aciklama' => 'Otopark durumu',
                    'display_order' => 8,
                ],
            ];

            foreach ($konforOzellikleri as $ozellik) {
                Ozellik::updateOrCreate(
                    ['slug' => $ozellik['slug']],
                    $ozellik
                );
            }
            $this->command->info("   → 8 konfor/eğlence özelliği eklendi");
        }

        $this->command->info("\n📊 YAZLIK KİRALIK ÖZELLİKLERİ RAPORU:");
        $this->command->info("   ✅ Özellik Kategorisi: 6");
        $this->command->info("   ✅ Toplam Özellik: 30");
        $this->command->info("   🏖️ Konaklama: 6");
        $this->command->info("   💰 Fiyatlandırma: 5");
        $this->command->info("   🏊 Havuz/Su Sporları: 7");
        $this->command->info("   🌳 Dış Mekan: 6");
        $this->command->info("   🛋️  İç Mekan: 8");
        $this->command->info("   📺 Konfor/Eğlence: 8");

        $this->command->info("\n🎯 ÖZEL NOTLAR:");
        $this->command->info("   • Günlük/haftalık/aylık fiyat → ilanlar tablosunda (field)");
        $this->command->info("   • Havuz → ilanlar tablosunda (field) + özellik olarak");
        $this->command->info("   • Denize mesafe → özellik olarak");
        $this->command->info("   • Tüm özellikler ZORUNLU DEĞİL (esnek)");
    }
}
