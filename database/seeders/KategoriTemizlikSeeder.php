<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriTemizlikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧹 Kategori temizliği başlıyor...');

        // 1. EV/DAİRE/VİLLA Kategorileri
        $this->createEvDaireVillaKategorileri();

        // 2. YAZLIK KİRALIK Kategorileri
        $this->createYazlikKiralikKategorileri();

        // 3. ARSA/ARAZİ Kategorileri
        $this->createArsaAraziKategorileri();

        $this->command->info('✅ Kategori temizliği tamamlandı!');
    }

    /**
     * Ev/Daire/Villa kategorileri
     */
    private function createEvDaireVillaKategorileri(): void
    {
        $this->command->info('🏠 Ev/Daire/Villa kategorileri ekleniyor...');

        $kategoriler = [
            [
                'name' => 'Oda Sayısı',
                'slug' => 'oda-sayisi',
                'veri_tipi' => 'number',
                'birim' => 'oda',
                'aciklama' => 'Toplam oda sayısı',
                'uyumlu_emlak_turleri' => json_encode(['ev', 'daire', 'villa']),
                'sira' => 1,
                'status' => true,
            ],
            [
                'name' => 'Banyo Sayısı',
                'slug' => 'banyo-sayisi',
                'veri_tipi' => 'number',
                'birim' => 'adet',
                'aciklama' => 'Toplam banyo sayısı',
                'uyumlu_emlak_turleri' => json_encode(['ev', 'daire', 'villa']),
                'sira' => 2,
                'status' => true,
            ],
            [
                'name' => 'Metrekare',
                'slug' => 'metrekare',
                'veri_tipi' => 'number',
                'birim' => 'm²',
                'aciklama' => 'Toplam kullanım alanı',
                'uyumlu_emlak_turleri' => json_encode(['ev', 'daire', 'villa']),
                'sira' => 3,
                'status' => true,
            ],
            [
                'name' => 'Balkon',
                'slug' => 'balkon',
                'veri_tipi' => 'checkbox',
                'aciklama' => 'Balkon var mı?',
                'uyumlu_emlak_turleri' => json_encode(['ev', 'daire', 'villa']),
                'sira' => 4,
                'status' => true,
            ],
            [
                'name' => 'Asansör',
                'slug' => 'asansor',
                'veri_tipi' => 'checkbox',
                'aciklama' => 'Asansör var mı?',
                'uyumlu_emlak_turleri' => json_encode(['ev', 'daire', 'villa']),
                'sira' => 5,
                'status' => true,
            ],
            [
                'name' => 'Güvenlik',
                'slug' => 'guvenlik',
                'veri_tipi' => 'checkbox',
                'aciklama' => 'Güvenlik sistemi var mı?',
                'uyumlu_emlak_turleri' => json_encode(['ev', 'daire', 'villa']),
                'sira' => 6,
                'status' => true,
            ],
            [
                'name' => 'Otopark',
                'slug' => 'otopark',
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode([
                    ['value' => 'kapali', 'label' => 'Kapalı Otopark'],
                    ['value' => 'acik', 'label' => 'Açık Otopark'],
                    ['value' => 'yok', 'label' => 'Otopark Yok'],
                ]),
                'aciklama' => 'Otopark statusu',
                'uyumlu_emlak_turleri' => json_encode(['ev', 'daire', 'villa']),
                'sira' => 7,
                'status' => true,
            ],
            [
                'name' => 'Isıtma',
                'slug' => 'isitma',
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode([
                    ['value' => 'dogalgaz', 'label' => 'Doğalgaz'],
                    ['value' => 'kombi', 'label' => 'Kombi'],
                    ['value' => 'kalorifer', 'label' => 'Kalorifer'],
                    ['value' => 'elektrik', 'label' => 'Elektrik'],
                    ['value' => 'klima', 'label' => 'Klima'],
                ]),
                'aciklama' => 'Isıtma sistemi',
                'uyumlu_emlak_turleri' => json_encode(['ev', 'daire', 'villa']),
                'sira' => 8,
                'status' => true,
            ],
            [
                'name' => 'Manzara',
                'slug' => 'manzara',
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode([
                    ['value' => 'deniz', 'label' => 'Deniz Manzarası'],
                    ['value' => 'dag', 'label' => 'Dağ Manzarası'],
                    ['value' => 'city', 'label' => 'Şehir Manzarası'],
                    ['value' => 'bahce', 'label' => 'Bahçe Manzarası'],
                    ['value' => 'yok', 'label' => 'Manzara Yok'],
                ]),
                'aciklama' => 'Manzara statusu',
                'uyumlu_emlak_turleri' => json_encode(['ev', 'daire', 'villa']),
                'sira' => 9,
                'status' => true,
            ],
        ];

        foreach ($kategoriler as $kategori) {
            $existing = DB::table('ozellik_kategorileri')->where('slug', $kategori['slug'])->first();
            if ($existing) {
                DB::table('ozellik_kategorileri')->where('slug', $kategori['slug'])->update($kategori);
            } else {
                $kategori['created_at'] = now();
                $kategori['updated_at'] = now();
                DB::table('ozellik_kategorileri')->insert($kategori);
            }
        }

        $this->command->info('✅ '.count($kategoriler).' Ev/Daire/Villa kategorisi eklendi.');
    }

    /**
     * Yazlık Kiralık kategorileri
     */
    private function createYazlikKiralikKategorileri(): void
    {
        $this->command->info('🏖️ Yazlık Kiralık kategorileri ekleniyor...');

        $kategoriler = [
            [
                'name' => 'Max Kişi Sayısı',
                'slug' => 'max-kisi-sayisi',
                'veri_tipi' => 'number',
                'birim' => 'kişi',
                'aciklama' => 'Maksimum konaklayabilecek kişi sayısı',
                'uyumlu_emlak_turleri' => json_encode(['yazlik-kiralik']),
                'sira' => 10,
                'status' => true,
            ],
            [
                'name' => 'Min Konaklama',
                'slug' => 'min-konaklama',
                'veri_tipi' => 'number',
                'birim' => 'gün',
                'aciklama' => 'Minimum konaklama süresi',
                'uyumlu_emlak_turleri' => json_encode(['yazlik-kiralik']),
                'sira' => 11,
                'status' => true,
            ],
            [
                'name' => 'Havuz',
                'slug' => 'havuz',
                'veri_tipi' => 'checkbox',
                'aciklama' => 'Havuz var mı?',
                'uyumlu_emlak_turleri' => json_encode(['yazlik-kiralik']),
                'sira' => 12,
                'status' => true,
            ],
            [
                'name' => 'Bahçe',
                'slug' => 'bahce',
                'veri_tipi' => 'checkbox',
                'aciklama' => 'Bahçe var mı?',
                'uyumlu_emlak_turleri' => json_encode(['yazlik-kiralik']),
                'sira' => 13,
                'status' => true,
            ],
            [
                'name' => 'Denize Uzaklık',
                'slug' => 'denize-uzaklik',
                'veri_tipi' => 'number',
                'birim' => 'metre',
                'aciklama' => 'Denize olan uzaklık',
                'uyumlu_emlak_turleri' => json_encode(['yazlik-kiralik']),
                'sira' => 14,
                'status' => true,
            ],
            [
                'name' => 'Belge No',
                'slug' => 'belge-no',
                'veri_tipi' => 'text',
                'aciklama' => 'T.C. Kültür ve Turizm Bakanlığı belge numarası',
                'uyumlu_emlak_turleri' => json_encode(['yazlik-kiralik']),
                'sira' => 15,
                'status' => true,
            ],
            [
                'name' => 'Dahil Hizmetler',
                'slug' => 'dahil-hizmetler',
                'veri_tipi' => 'checkbox',
                'aciklama' => 'Fiyata dahil olan hizmetler',
                'uyumlu_emlak_turleri' => json_encode(['yazlik-kiralik']),
                'sira' => 16,
                'status' => true,
            ],
        ];

        foreach ($kategoriler as $kategori) {
            $existing = DB::table('ozellik_kategorileri')->where('slug', $kategori['slug'])->first();
            if ($existing) {
                DB::table('ozellik_kategorileri')->where('slug', $kategori['slug'])->update($kategori);
            } else {
                $kategori['created_at'] = now();
                $kategori['updated_at'] = now();
                DB::table('ozellik_kategorileri')->insert($kategori);
            }
        }

        $this->command->info('✅ '.count($kategoriler).' Yazlık Kiralık kategorisi eklendi.');
    }

    /**
     * Arsa/Arazi kategorileri
     */
    private function createArsaAraziKategorileri(): void
    {
        $this->command->info('🌍 Arsa/Arazi kategorileri ekleniyor...');

        $kategoriler = [
            [
                'name' => 'Alan',
                'slug' => 'alan',
                'veri_tipi' => 'number',
                'birim' => 'm²',
                'aciklama' => 'Toplam arsa alanı',
                'uyumlu_emlak_turleri' => json_encode(['arsa', 'arazi']),
                'sira' => 17,
                'status' => true,
            ],
            [
                'name' => 'İmar Durumu',
                'slug' => 'imar-statusu',
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode([
                    ['value' => 'imarli', 'label' => 'İmarlı'],
                    ['value' => 'imarsiz', 'label' => 'İmarsız'],
                    ['value' => 'kismi-imar', 'label' => 'Kısmi İmar'],
                ]),
                'aciklama' => 'Arsa imar statusu',
                'uyumlu_emlak_turleri' => json_encode(['arsa', 'arazi']),
                'sira' => 18,
                'status' => true,
            ],
            [
                'name' => 'TAKS',
                'slug' => 'taks',
                'veri_tipi' => 'number',
                'aciklama' => 'Toplam Alan Kullanım Katsayısı',
                'uyumlu_emlak_turleri' => json_encode(['arsa', 'arazi']),
                'sira' => 19,
                'status' => true,
            ],
            [
                'name' => 'KAKS',
                'slug' => 'kaks',
                'veri_tipi' => 'number',
                'aciklama' => 'Kat Alanı Kullanım Katsayısı',
                'uyumlu_emlak_turleri' => json_encode(['arsa', 'arazi']),
                'sira' => 20,
                'status' => true,
            ],
            [
                'name' => 'Tapu Durumu',
                'slug' => 'tapu-statusu',
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode([
                    ['value' => 'kat-mulkiyeti', 'label' => 'Kat Mülkiyeti'],
                    ['value' => 'arsa-tapusu', 'label' => 'Arsa Tapusu'],
                    ['value' => 'hisseli-tapu', 'label' => 'Hisseli Tapu'],
                    ['value' => 'irtifak', 'label' => 'İrtifak'],
                ]),
                'aciklama' => 'Tapu statusu',
                'uyumlu_emlak_turleri' => json_encode(['arsa', 'arazi']),
                'sira' => 21,
                'status' => true,
            ],
            [
                'name' => 'Altyapı',
                'slug' => 'altyapi',
                'veri_tipi' => 'checkbox',
                'aciklama' => 'Altyapı mevcut mu?',
                'uyumlu_emlak_turleri' => json_encode(['arsa', 'arazi']),
                'sira' => 22,
                'status' => true,
            ],
            [
                'name' => 'Cadde Üzeri',
                'slug' => 'cadde-uzeri',
                'veri_tipi' => 'checkbox',
                'aciklama' => 'Cadde üzerinde mi?',
                'uyumlu_emlak_turleri' => json_encode(['arsa', 'arazi']),
                'sira' => 23,
                'status' => true,
            ],
        ];

        foreach ($kategoriler as $kategori) {
            $existing = DB::table('ozellik_kategorileri')->where('slug', $kategori['slug'])->first();
            if ($existing) {
                DB::table('ozellik_kategorileri')->where('slug', $kategori['slug'])->update($kategori);
            } else {
                $kategori['created_at'] = now();
                $kategori['updated_at'] = now();
                DB::table('ozellik_kategorileri')->insert($kategori);
            }
        }

        $this->command->info('✅ '.count($kategoriler).' Arsa/Arazi kategorisi eklendi.');
    }
}
