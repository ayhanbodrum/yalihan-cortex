<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YazlikKiralamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sezon verileri
        $sezonlar = [
            [
                'sezon_adi' => 'Yaz Sezonu (Yüksek)',
                'baslangic_tarihi' => '2025-06-01',
                'bitis_tarihi' => '2025-08-31',
                'fiyat_carpani' => 1.5,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sezon_adi' => 'Ara Sezon (Orta)',
                'baslangic_tarihi' => '2025-04-01',
                'bitis_tarihi' => '2025-05-31',
                'fiyat_carpani' => 1.2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sezon_adi' => 'Ara Sezon (Orta)',
                'baslangic_tarihi' => '2025-09-01',
                'bitis_tarihi' => '2025-10-31',
                'fiyat_carpani' => 1.2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sezon_adi' => 'Kış Sezonu (Düşük)',
                'baslangic_tarihi' => '2025-11-01',
                'bitis_tarihi' => '2026-03-31',
                'fiyat_carpani' => 0.8,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('ilan_takvim_sezonlar')->insert($sezonlar);

        // Örnek yazlık fiyatlandırma (eğer ilan varsa)
        $ilanlar = DB::table('ilanlar')->where('emlak_turu', 'villa')->orWhere('emlak_turu', 'yazlik')->limit(3)->get();

        foreach ($ilanlar as $ilan) {
            $sezonlar = DB::table('ilan_takvim_sezonlar')->get();

            foreach ($sezonlar as $sezon) {
                $gunlukFiyat = 0;

                switch ($sezon->sezon_adi) {
                    case 'Yaz Sezonu (Yüksek)':
                        $gunlukFiyat = 2500;
                        break;
                    case 'Ara Sezon (Orta)':
                        $gunlukFiyat = 1800;
                        break;
                    case 'Kış Sezonu (Düşük)':
                        $gunlukFiyat = 1200;
                        break;
                }

                DB::table('ilan_takvim_fiyatlandirma')->insert([
                    'ilan_id' => $ilan->id,
                    'sezon_id' => $sezon->id,
                    'gunluk_fiyat' => $gunlukFiyat,
                    'haftalik_fiyat' => $gunlukFiyat * 6, // 1 gün indirim
                    'aylik_fiyat' => $gunlukFiyat * 25, // 5 gün indirim
                    'minimum_gece' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // YENİ: Mesafe Cetveli Örnek Verileri
            $mesafeVerileri = [
                [
                    'nokta_adi' => 'Mini Market',
                    'nokta_tipi' => 'market',
                    'mesafe_km' => 0.3,
                    'yurume_suresi_dk' => 4,
                    'arac_suresi_dk' => 1,
                    'adres' => 'Üzümlü Mah. Ana Cadde No:15',
                    'telefon' => '0252 123 45 67',
                    'status' => true,
                ],
                [
                    'nokta_adi' => 'Restaurant',
                    'nokta_tipi' => 'restaurant',
                    'mesafe_km' => 0.3,
                    'yurume_suresi_dk' => 4,
                    'arac_suresi_dk' => 1,
                    'adres' => 'Üzümlü Mah. Sahil Caddesi No:8',
                    'telefon' => '0252 123 45 68',
                    'status' => true,
                ],
                [
                    'nokta_adi' => 'Toplu Taşıma',
                    'nokta_tipi' => 'transport',
                    'mesafe_km' => 7.0,
                    'yurume_suresi_dk' => null,
                    'arac_suresi_dk' => 12,
                    'adres' => 'Kalkan Merkez Otobüs Durağı',
                    'telefon' => null,
                    'status' => true,
                ],
                [
                    'nokta_adi' => 'Deniz',
                    'nokta_tipi' => 'sea',
                    'mesafe_km' => 2.0,
                    'yurume_suresi_dk' => 25,
                    'arac_suresi_dk' => 4,
                    'adres' => 'Kalkan Sahil Plajı',
                    'telefon' => null,
                    'status' => true,
                ],
                [
                    'nokta_adi' => 'Dalaman Havaalanı',
                    'nokta_tipi' => 'airport',
                    'mesafe_km' => 50.0,
                    'yurume_suresi_dk' => null,
                    'arac_suresi_dk' => 45,
                    'adres' => 'Dalaman Havalimanı, Muğla',
                    'telefon' => '0252 792 50 00',
                    'status' => true,
                ],
                [
                    'nokta_adi' => 'En Yakın Merkez',
                    'nokta_tipi' => 'center',
                    'mesafe_km' => 16.0,
                    'yurume_suresi_dk' => null,
                    'arac_suresi_dk' => 20,
                    'adres' => 'Kalkan Merkez',
                    'telefon' => null,
                    'status' => true,
                ],
            ];

            foreach ($mesafeVerileri as $mesafe) {
                DB::table('yazlik_mesafe_cetveli')->insert([
                    'ilan_id' => $ilan->id,
                    'nokta_adi' => $mesafe['nokta_adi'],
                    'nokta_tipi' => $mesafe['nokta_tipi'],
                    'mesafe_km' => $mesafe['mesafe_km'],
                    'yurume_suresi_dk' => $mesafe['yurume_suresi_dk'],
                    'arac_suresi_dk' => $mesafe['arac_suresi_dk'],
                    'adres' => $mesafe['adres'],
                    'telefon' => $mesafe['telefon'],
                    'status' => $mesafe['active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // YENİ: Fiyata Dahil Hizmetler Örnek Verileri
            $hizmetVerileri = [
                [
                    'hizmet_adi' => 'Elektrik Kullanımı',
                    'hizmet_tipi' => 'utility',
                    'aciklama' => 'Günlük elektrik tüketimi dahil',
                    'birim' => 'kW',
                    'miktar' => 10.0,
                    'sınırsız' => false,
                    'status' => true,
                ],
                [
                    'hizmet_adi' => 'Su Kullanımı',
                    'hizmet_tipi' => 'utility',
                    'aciklama' => 'Günlük su tüketimi dahil',
                    'birim' => 'm³',
                    'miktar' => 2.0,
                    'sınırsız' => false,
                    'status' => true,
                ],
                [
                    'hizmet_adi' => 'Tüpgaz Kullanımı',
                    'hizmet_tipi' => 'utility',
                    'aciklama' => 'Mutfak ve ısınma için tüpgaz',
                    'birim' => 'adet',
                    'miktar' => 1.0,
                    'sınırsız' => false,
                    'status' => true,
                ],
                [
                    'hizmet_adi' => 'Havuz Kullanımı',
                    'hizmet_tipi' => 'amenity',
                    'aciklama' => 'Özel havuz kullanımı',
                    'birim' => null,
                    'miktar' => null,
                    'sınırsız' => true,
                    'status' => true,
                ],
                [
                    'hizmet_adi' => 'Bahçe Kullanımı',
                    'hizmet_tipi' => 'amenity',
                    'aciklama' => 'Özel bahçe ve barbekü alanı',
                    'birim' => null,
                    'miktar' => null,
                    'sınırsız' => true,
                    'status' => true,
                ],
                [
                    'hizmet_adi' => 'Giriş Temizliği',
                    'hizmet_tipi' => 'cleaning',
                    'aciklama' => 'Giriş öncesi profesyonel temizlik',
                    'birim' => 'kez',
                    'miktar' => 1.0,
                    'sınırsız' => false,
                    'status' => true,
                ],
                [
                    'hizmet_adi' => 'İnternet',
                    'hizmet_tipi' => 'utility',
                    'aciklama' => 'Yüksek hızlı Wi-Fi internet',
                    'birim' => null,
                    'miktar' => null,
                    'sınırsız' => true,
                    'status' => true,
                ],
            ];

            foreach ($hizmetVerileri as $hizmet) {
                DB::table('yazlik_fiyata_dahil_hizmetler')->insert([
                    'ilan_id' => $ilan->id,
                    'hizmet_adi' => $hizmet['hizmet_adi'],
                    'hizmet_tipi' => $hizmet['hizmet_tipi'],
                    'aciklama' => $hizmet['aciklama'],
                    'birim' => $hizmet['birim'],
                    'miktar' => $hizmet['miktar'],
                    'sınırsız' => $hizmet['sınırsız'],
                    'status' => $hizmet['active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // YENİ: Yatak Seçenekleri Örnek Verileri
            $yatakVerileri = [
                [
                    'oda_adi' => 'Yatak Odası 1',
                    'yatak_tipi' => 'cift_kisilik',
                    'yatak_sayisi' => 1,
                    'kisi_kapasitesi' => 2,
                    'ozellikler' => 'Klima, TV, Balkon, Deniz Manzarası',
                    'status' => true,
                ],
                [
                    'oda_adi' => 'Yatak Odası 2',
                    'yatak_tipi' => 'cift_kisilik',
                    'yatak_sayisi' => 1,
                    'kisi_kapasitesi' => 2,
                    'ozellikler' => 'Klima, TV, Bahçe Manzarası',
                    'status' => true,
                ],
                [
                    'oda_adi' => 'Yatak Odası 3',
                    'yatak_tipi' => 'tek_kisilik',
                    'yatak_sayisi' => 2,
                    'kisi_kapasitesi' => 2,
                    'ozellikler' => 'Klima, TV',
                    'status' => true,
                ],
            ];

            foreach ($yatakVerileri as $yatak) {
                DB::table('yazlik_yatak_secenekleri')->insert([
                    'ilan_id' => $ilan->id,
                    'oda_adi' => $yatak['oda_adi'],
                    'yatak_tipi' => $yatak['yatak_tipi'],
                    'yatak_sayisi' => $yatak['yatak_sayisi'],
                    'kisi_kapasitesi' => $yatak['kisi_kapasitesi'],
                    'ozellikler' => $yatak['ozellikler'],
                    'status' => $yatak['active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // YENİ: Konut İzin Belgeleri Örnek Verileri
            $izinVerileri = [
                [
                    'belge_tipi' => 'eids',
                    'belge_no' => '48-392',
                    'veren_kurum' => 'Kültür ve Turizm Bakanlığı',
                    'verilis_tarihi' => '2024-01-15',
                    'gecerlilik_tarihi' => '2029-01-15',
                    'aciklama' => 'EİDS Konut İzin Belgesi',
                    'status' => true,
                ],
                [
                    'belge_tipi' => 'turizm_tescil',
                    'belge_no' => '48-392',
                    'veren_kurum' => 'Kültür ve Turizm Bakanlığı',
                    'verilis_tarihi' => '2024-01-15',
                    'gecerlilik_tarihi' => '2029-01-15',
                    'aciklama' => 'Turizm Tescil Belgesi',
                    'status' => true,
                ],
            ];

            foreach ($izinVerileri as $izin) {
                DB::table('yazlik_konut_izin_belgeleri')->insert([
                    'ilan_id' => $ilan->id,
                    'belge_tipi' => $izin['belge_tipi'],
                    'belge_no' => $izin['belge_no'],
                    'veren_kurum' => $izin['veren_kurum'],
                    'verilis_tarihi' => $izin['verilis_tarihi'],
                    'gecerlilik_tarihi' => $izin['gecerlilik_tarihi'],
                    'aciklama' => $izin['aciklama'],
                    'status' => $izin['active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Örnek rezervasyon (eğer ilan varsa)
        if ($ilanlar->count() > 0) {
            $ilan = $ilanlar->first();

            DB::table('yazlik_rezervasyonlar')->insert([
                'ilan_id' => $ilan->id,
                'rezervasyon_kodu' => 'RZ'.date('Y').str_pad($ilan->id, 3, '0', STR_PAD_LEFT).'001',
                'musteri_adi' => 'Ahmet Yılmaz',
                'musteri_telefon' => '0532 123 45 67',
                'musteri_email' => 'ahmet@example.com',
                'check_in' => '2025-07-15',
                'check_out' => '2025-07-22',
                'misafir_sayisi' => 4,
                'cocuk_sayisi' => 1,
                'pet_sayisi' => 0,
                'ozel_istekler' => 'Deniz manzaralı oda tercih edilir',
                'toplam_fiyat' => 17500,
                'kapora_tutari' => 5250,
                'status' => 'onaylandi',
                'onay_tarihi' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Yazlık Kiralama seeder tamamlandı!');
        $this->command->info('Eklenen özellikler:');
        $this->command->info('- Mesafe Cetveli (6 nokta)');
        $this->command->info('- Fiyata Dahil Hizmetler (7 hizmet)');
        $this->command->info('- Yatak Seçenekleri (3 oda)');
        $this->command->info('- Konut İzin Belgeleri (2 belge)');
    }
}
