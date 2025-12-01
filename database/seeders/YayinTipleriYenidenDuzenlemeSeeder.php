<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YayinTipleriYenidenDuzenlemeSeeder extends Seeder
{
    /**
     * 21 Yayın Tipi - Detaylı Açıklamalar ve Mantıklı Yapı
     * Context7 Compliant - 2025-10-23
     *
     * Mevcut sistemle uyumlu, açıklamalar eklenmiş
     */
    public function run(): void
    {
        $this->command->info("📢 Yayın tipleri güncelleniyor...\n");

        // ✅ YAYIN TİPİ DETAYLI TANIMLARI
        $yayinTipleriDetay = [
            // ===== ANA SATIŞ TİPLERİ =====
            [
                'yayin_tipi' => 'Satılık',
                'aciklama' => 'Mülkiyeti devredilecek gayrimenkul. Tapu devri ile satış. Tüm kategoriler için geçerli.',
                'kategori_uygunluk' => 'Tüm kategoriler',
                'min_fiyat' => 50000,
                'icon' => '💰',
                'populer' => true,
                'sira' => 1,
            ],
            [
                'yayin_tipi' => 'Kiralık',
                'aciklama' => 'Uzun dönemli kiralık (aylık/yıllık). Konut, işyeri ve ofisler için uygun.',
                'kategori_uygunluk' => 'Konut, İşyeri, Ofis',
                'min_konaklama' => 30,
                'icon' => '🔑',
                'populer' => true,
                'sira' => 2,
            ],
            [
                'yayin_tipi' => 'Günlük Kiralık',
                'aciklama' => 'Kısa süreli kiralık (1-29 gün). Yazlık, apart, otel tipi konaklama için.',
                'kategori_uygunluk' => 'Yazlık, Apart, Residence, Daire',
                'min_konaklama' => 1,
                'max_konaklama' => 29,
                'icon' => '📅',
                'populer' => true,
                'sira' => 3,
            ],
            [
                'yayin_tipi' => 'Sezonluk Kiralık',
                'aciklama' => 'Yaz/kış sezonluk kiralık (3-6 ay). Yazlık ve tatil köyleri için.',
                'kategori_uygunluk' => 'Yazlık, Villa, Müstakil',
                'min_konaklama' => 90,
                'max_konaklama' => 180,
                'icon' => '🌞',
                'populer' => true,
                'sira' => 4,
            ],

            // ===== ÖZEL SATIŞ MODELLERİ =====
            [
                'yayin_tipi' => 'Kat Karşılığı',
                'aciklama' => 'Arsa sahibi ile müteahhit anlaşması. Arsa karşılığı daire/villa alımı. Sadece arsalar için.',
                'kategori_uygunluk' => 'Arsa (İmarlı)',
                'min_alan_m2' => 200,
                'icon' => '🏗️',
                'populer' => true,
                'sira' => 5,
            ],
            [
                'yayin_tipi' => 'Devren Satılık',
                'aciklama' => 'İşletme halindeki işyeri devri. Ciro, müşteri portföyü ve ekipmanlarla birlikte satış.',
                'kategori_uygunluk' => 'İşyeri, Restoran, Kafe, Otel',
                'icon' => '🏢',
                'populer' => false,
                'sira' => 6,
            ],
            [
                'yayin_tipi' => 'Devren Kiralık',
                'aciklama' => 'İşletme halindeki işyeri kiralama devri. Ruhsat ve ekipmanlarla.',
                'kategori_uygunluk' => 'İşyeri, Restoran, Dükkan',
                'icon' => '🔄',
                'populer' => false,
                'sira' => 7,
            ],
            [
                'yayin_tipi' => 'Trampalı',
                'aciklama' => 'Takas ile satış. Başka gayrimenkul ile değişim kabul edilir.',
                'kategori_uygunluk' => 'Tüm kategoriler',
                'icon' => '🔀',
                'populer' => false,
                'sira' => 8,
            ],

            // ===== YATIRIM VE FİNANS =====
            [
                'yayin_tipi' => 'Yatırımlık',
                'aciklama' => 'Yatırım amaçlı satılık. Yüksek değer artış potansiyeli, kira getirisi.',
                'kategori_uygunluk' => 'Tüm kategoriler',
                'icon' => '📈',
                'populer' => true,
                'sira' => 9,
            ],
            [
                'yayin_tipi' => 'Krediye Uygun',
                'aciklama' => 'Banka kredisi çekilebilen gayrimenkul. Tapu/imar uygun.',
                'kategori_uygunluk' => 'Konut, Arsa, İşyeri',
                'icon' => '🏦',
                'populer' => false,
                'sira' => 10,
            ],
            [
                'yayin_tipi' => 'Sosyal Konut',
                'aciklama' => 'TOKİ veya belediye sosyal konut projeleri. Uygun fiyatlı konut.',
                'kategori_uygunluk' => 'Daire, Residence',
                'icon' => '🏘️',
                'populer' => false,
                'sira' => 11,
            ],

            // ===== SATIŞ AŞAMASI =====
            [
                'yayin_tipi' => 'Ön Satış',
                'aciklama' => 'Proje aşamasında satış. İnşaat başlamadan veya devam ederken satış.',
                'kategori_uygunluk' => 'Projeler, Residence, Konut',
                'icon' => '📋',
                'populer' => false,
                'sira' => 12,
            ],
            [
                'yayin_tipi' => 'İnşaat Halinde',
                'aciklama' => 'İnşaatı devam eden gayrimenkul. Teslim tarihi belirtilir.',
                'kategori_uygunluk' => 'Tüm kategoriler',
                'icon' => '🏗️',
                'populer' => false,
                'sira' => 13,
            ],
            [
                'yayin_tipi' => 'Sıfır/Yeni',
                'aciklama' => 'Hiç kullanılmamış, yeni teslim gayrimenkul. Sıfır daire/villa.',
                'kategori_uygunluk' => 'Konut, Villa, Daire',
                'icon' => '✨',
                'populer' => true,
                'sira' => 14,
            ],

            // ===== ÖZEL DURUMLAR =====
            [
                'yayin_tipi' => 'Acil Satılık',
                'aciklama' => 'Hızlı satış için pazarlıklı fiyat. Acil durum, taşınma, finansal ihtiyaç.',
                'kategori_uygunluk' => 'Tüm kategoriler',
                'icon' => '🔥',
                'populer' => false,
                'sira' => 15,
            ],
            [
                'yayin_tipi' => 'Sahibinden',
                'aciklama' => 'Emlakçı komisyonu yok. Doğrudan mal sahibinden satış/kiralık.',
                'kategori_uygunluk' => 'Tüm kategoriler',
                'icon' => '👤',
                'populer' => true,
                'sira' => 16,
            ],
            [
                'yayin_tipi' => 'Lüks Segment',
                'aciklama' => 'Lüks gayrimenkul. Premium lokasyon, yüksek kalite, özel hizmetler.',
                'kategori_uygunluk' => 'Villa, Residence, Penthouse',
                'min_fiyat' => 5000000,
                'icon' => '💎',
                'populer' => false,
                'sira' => 17,
            ],
            [
                'yayin_tipi' => 'İhaleli Satış',
                'aciklama' => 'Mahkeme, banka veya devlet ihalesi ile satış. İcra/tasfiye.',
                'kategori_uygunluk' => 'Tüm kategoriler',
                'icon' => '⚖️',
                'populer' => false,
                'sira' => 18,
            ],

            // ===== ÖZEL KULLANIM =====
            [
                'yayin_tipi' => 'Öğrenci Evi',
                'aciklama' => 'Öğrencilere uygun kiralık konut. Üniversite yakını, uygun fiyat.',
                'kategori_uygunluk' => 'Daire, Müstakil, Apart',
                'max_fiyat' => 15000,
                'icon' => '🎓',
                'populer' => false,
                'sira' => 19,
            ],
            [
                'yayin_tipi' => 'Ofis Dönüşümlü',
                'aciklama' => 'Ofisten konuta veya konuttan ofise dönüştürülebilir gayrimenkul.',
                'kategori_uygunluk' => 'Daire, Ofis, İşyeri',
                'icon' => '🔄',
                'populer' => false,
                'sira' => 20,
            ],
            [
                'yayin_tipi' => 'Devren',
                'aciklama' => 'İşletme devri. Ciro, müşteri portföyü, ruhsat ve ekipmanlarla birlikte.',
                'kategori_uygunluk' => 'İşyeri, Tüm Ticari',
                'icon' => '🏪',
                'populer' => false,
                'sira' => 21,
            ],
        ];

        $this->command->info("📊 YAYIN TİPİ DETAYLARI GÜNCELLENİYOR:\n");

        foreach ($yayinTipleriDetay as $detay) {
            // Mevcut kayıtları güncelle (sadece tabloda olan column'lar)
            $updated = DB::table('ilan_kategori_yayin_tipleri')
                ->where('yayin_tipi', $detay['yayin_tipi'])
                ->update([
                    'aciklama' => $detay['aciklama'],
                    'icon' => $detay['icon'] ?? null,
                    'populer' => $detay['populer'] ?? false,
                    'sira' => $detay['sira'] ?? 0,
                ]);

            if ($updated > 0) {
                $this->command->info("   ✅ {$detay['icon']} {$detay['yayin_tipi']} - {$updated} kayıt güncellendi");
            }
        }

        $this->command->info("\n🎯 ÖZELLİKLE YAZLIK İÇİN:");
        $this->command->info('   ✅ Günlük Kiralık (1-29 gün)');
        $this->command->info('   ✅ Sezonluk Kiralık (3-6 ay)');
        $this->command->info('   ✅ Kiralık (aylık/yıllık)');

        $this->command->info("\n📋 YAYIN TİPİ KATEGORİLERİ:");
        $this->command->info('   🏠 Ana Satış (4): Satılık, Kiralık, Günlük, Sezonluk');
        $this->command->info('   🏗️ Özel Modeller (4): Kat Karşılığı, Devren Sat/Kira, Trampalı');
        $this->command->info('   📈 Yatırım (3): Yatırımlık, Krediye Uygun, Sosyal Konut');
        $this->command->info('   🏗️ Aşama (3): Ön Satış, İnşaat Halinde, Sıfır');
        $this->command->info('   ⚡ Özel Durum (4): Acil, Sahibinden, Lüks, İhaleli');
        $this->command->info('   🎯 Özel Kullanım (3): Öğrenci Evi, Ofis Dönüşümlü, Devren');
    }
}
