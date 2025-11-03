<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ozellik;
use App\Models\IlanKategori;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KonutTemelOzelliklerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Context7: Konut ve alt kategorileri için temel özellikler
     * Bu özellikler emlak sektöründe STANDART olarak kullanılır
     */
    public function run(): void
    {
        // Kategorileri al
        $konut = IlanKategori::where('name', 'Konut')->first();
        $daire = IlanKategori::where('name', 'Daire')->first();

        if (!$konut) {
            $this->command->error('❌ Konut kategorisi bulunamadı!');
            return;
        }

        $this->command->info('🏠 Konut Temel Özellikleri Ekleniyor...');
        $this->command->newLine();

        // ═══════════════════════════════════════════════════════════
        // 1️⃣  KONUT GENEL ÖZELLİKLER (Tüm alt kategoriler için)
        // ═══════════════════════════════════════════════════════════

        $konutOzellikleri = [
            [
                'name' => 'Oda Sayısı',
                'slug' => 'oda-sayisi',
                'veri_tipi' => 'text',
                'veri_secenekleri' => null,
                'birim' => null,
                'zorunlu' => true,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'order' => 1,
                'aciklama' => 'Örn: 1+1, 2+1, 3+1, 4+1, Stüdyo'
            ],
            [
                'name' => 'Brüt Metrekare',
                'slug' => 'brut-metrekare',
                'veri_tipi' => 'number',
                'veri_secenekleri' => null,
                'birim' => 'm²',
                'zorunlu' => true,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'order' => 2,
                'aciklama' => 'Brüt kullanım alanı'
            ],
            [
                'name' => 'Net Metrekare',
                'slug' => 'net-metrekare',
                'veri_tipi' => 'number',
                'veri_secenekleri' => null,
                'birim' => 'm²',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => false,
                'order' => 3,
                'aciklama' => 'Net kullanım alanı (duvarlar hariç)'
            ],
            [
                'name' => 'Banyo Sayısı',
                'slug' => 'banyo-sayisi',
                'veri_tipi' => 'number',
                'veri_secenekleri' => null,
                'birim' => null,
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'order' => 4,
                'aciklama' => 'Toplam banyo/tuvalet sayısı'
            ],
            [
                'name' => 'Bina Yaşı',
                'slug' => 'bina-yasi',
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode([
                    '0' => '0 (Sıfır Bina)',
                    '1-5' => '1-5 Yıl',
                    '6-10' => '6-10 Yıl',
                    '11-15' => '11-15 Yıl',
                    '16-20' => '16-20 Yıl',
                    '21-25' => '21-25 Yıl',
                    '26+' => '26+ Yıl'
                ]),
                'birim' => null,
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => false,
                'order' => 5,
                'aciklama' => 'Binanın yapım tarihi'
            ],
            [
                'name' => 'Kat',
                'slug' => 'kat',
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode([
                    'Bodrum' => 'Bodrum Kat',
                    'Zemin' => 'Zemin Kat',
                    '1' => '1. Kat',
                    '2' => '2. Kat',
                    '3' => '3. Kat',
                    '4' => '4. Kat',
                    '5' => '5. Kat',
                    '6' => '6. Kat',
                    '7' => '7. Kat',
                    '8' => '8. Kat',
                    '9' => '9. Kat',
                    '10' => '10. Kat',
                    '10+' => '10+ Kat',
                    'Çatı Katı' => 'Çatı Katı',
                    'Müstakil' => 'Müstakil'
                ]),
                'birim' => null,
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'order' => 6,
                'aciklama' => 'Bulunduğu kat'
            ],
            [
                'name' => 'Toplam Kat',
                'slug' => 'toplam-kat',
                'veri_tipi' => 'number',
                'veri_secenekleri' => null,
                'birim' => null,
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => false,
                'order' => 7,
                'aciklama' => 'Binadaki toplam kat sayısı'
            ],
            [
                'name' => 'Isıtma',
                'slug' => 'isitma',
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode([
                    'Yok' => 'Yok',
                    'Soba' => 'Soba',
                    'Doğalgaz (Kombi)' => 'Doğalgaz (Kombi)',
                    'Doğalgaz (Merkezi)' => 'Doğalgaz (Merkezi)',
                    'Kat Kaloriferi' => 'Kat Kaloriferi',
                    'Merkezi Sistem' => 'Merkezi Sistem',
                    'Yerden Isıtma' => 'Yerden Isıtma',
                    'Klima' => 'Klima',
                    'Fancoil Ünitesi' => 'Fancoil Ünitesi',
                    'Güneş Enerjisi' => 'Güneş Enerjisi',
                    'Elektrikli Radyatör' => 'Elektrikli Radyatör',
                    'Jeotermal' => 'Jeotermal',
                    'VRV' => 'VRV'
                ]),
                'birim' => null,
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => false,
                'order' => 8,
                'aciklama' => 'Isınma sistemi türü'
            ],
            [
                'name' => 'Cephe',
                'slug' => 'cephe',
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode([
                    'Kuzey' => 'Kuzey',
                    'Güney' => 'Güney',
                    'Doğu' => 'Doğu',
                    'Batı' => 'Batı',
                    'Güneydoğu' => 'Güneydoğu',
                    'Güneybatı' => 'Güneybatı',
                    'Kuzeydoğu' => 'Kuzeydoğu',
                    'Kuzeybatı' => 'Kuzeybatı'
                ]),
                'birim' => null,
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => false,
                'order' => 9,
                'aciklama' => 'Konutun hangi yöne baktığı'
            ],
            [
                'name' => 'Balkon',
                'slug' => 'balkon',
                'veri_tipi' => 'boolean',
                'veri_secenekleri' => null,
                'birim' => null,
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'order' => 10,
                'aciklama' => 'Balkon var mı?'
            ],
            [
                'name' => 'Asansör',
                'slug' => 'asansor',
                'veri_tipi' => 'boolean',
                'veri_secenekleri' => null,
                'birim' => null,
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'order' => 11,
                'aciklama' => 'Asansör var mı?'
            ],
            [
                'name' => 'Otopark',
                'slug' => 'otopark',
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode([
                    'Yok' => 'Yok',
                    'Açık Otopark' => 'Açık Otopark',
                    'Kapalı Otopark' => 'Kapalı Otopark'
                ]),
                'birim' => null,
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'order' => 12,
                'aciklama' => 'Otopark durumu'
            ],
            [
                'name' => 'Eşyalı',
                'slug' => 'esyali',
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode([
                    'Hayır' => 'Eşyasız',
                    'Kısmen' => 'Kısmen Eşyalı',
                    'Evet' => 'Tam Eşyalı'
                ]),
                'birim' => null,
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'order' => 13,
                'aciklama' => 'Eşyalı mı?'
            ],
            [
                'name' => 'Kullanım Durumu',
                'slug' => 'kullanim-durumu',
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode([
                    'Boş' => 'Boş',
                    'Kiracılı' => 'Kiracılı',
                    'Mülk Sahibi' => 'Mülk Sahibi'
                ]),
                'birim' => null,
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => false,
                'order' => 14,
                'aciklama' => 'Mevcut kullanım durumu'
            ],
            [
                'name' => 'Site İçerisinde',
                'slug' => 'site-icerisinde',
                'veri_tipi' => 'boolean',
                'veri_secenekleri' => null,
                'birim' => null,
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'order' => 15,
                'aciklama' => 'Site içerisinde mi?'
            ],
        ];

        foreach ($konutOzellikleri as $ozellik) {
            // Önce slug'a göre kontrol et (duplicate önleme - GLOBAL check)
            $existing = Ozellik::where('slug', $ozellik['slug'])->first();

            if (!$existing) {
                Ozellik::create([
                    'kategori_id' => $konut->id,
                    'name' => $ozellik['name'],
                    'slug' => $ozellik['slug'],
                    'veri_tipi' => $ozellik['veri_tipi'],
                    'veri_secenekleri' => $ozellik['veri_secenekleri'],
                    'birim' => $ozellik['birim'],
                    'status' => 'aktif',
                    'order' => $ozellik['order'],
                    'zorunlu' => $ozellik['zorunlu'],
                    'arama_filtresi' => $ozellik['arama_filtresi'],
                    'ilan_kartinda_goster' => $ozellik['ilan_kartinda_goster'],
                    'aciklama' => $ozellik['aciklama']
                ]);

                $this->command->info("  ✅ {$ozellik['name']} eklendi");
            } else {
                $this->command->warn("  ⚠️  {$ozellik['name']} zaten mevcut");
            }
        }

        $this->command->newLine();
        $this->command->info('🎉 Konut temel özellikleri başarıyla eklendi!');
        $this->command->newLine();
        $this->command->line('📊 İstatistikler:');
        $this->command->line('  • Kategori: Konut (ID: ' . $konut->id . ')');
        $this->command->line('  • Eklenen Özellik: ' . count($konutOzellikleri) . ' adet');
        $this->command->line('  • Tüm alt kategoriler bu özellikleri kullanabilir');
    }
}
