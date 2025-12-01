<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TuristikTesisModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Turistik Tesis Modülü Seeder başlatılıyor...');

        // Örnek turistik tesis detayları
        $this->seedTuristikTesisDetaylari();

        // Örnek turistik tesis özellikleri
        $this->seedTuristikTesisOzellikleri();

        // Örnek rezervasyon takvimi
        $this->seedRezervasyonTakvimi();

        $this->command->info('Turistik Tesis Modülü Seeder tamamlandı!');
    }

    /**
     * Turistik tesis detayları için örnek veriler
     */
    private function seedTuristikTesisDetaylari(): void
    {
        $this->command->info('Turistik tesis detayları ekleniyor...');

        // Örnek veriler (gerçek ilan_id'ler ile değiştirilmeli)
        $turistikTesisler = [
            [
                'ilan_id' => 1, // Örnek ilan ID
                'tesis_tipi' => 'otel',
                'tesis_alt_tipi' => '5 Yıldız',
                'tesis_kategorisi' => 'lüks',
                'toplam_oda_sayisi' => 150,
                'toplam_yatak_sayisi' => 300,
                'maksimum_kapasite' => 350,
                'minimum_kapasite' => 1,
                'yildiz_sayisi' => 5,
                'kalite_sinifi' => 'lüks',
                'tesis_standarti' => 'uluslararası',
                'pansiyon_turu' => 'ai',
                'pansiyon_detaylari' => 'All Inclusive paket hizmeti',
                'yemek_hizmeti' => true,
                'yemek_turleri' => json_encode(['kahvaltı', 'öğle', 'akşam', 'snack']),
                'oda_tipleri' => json_encode(['standart', 'deluxe', 'suite', 'başkanlık']),
                'oda_ozellikleri' => json_encode(['klima', 'tv', 'wifi', 'mini_bar', 'balkon']),
                'oda_metrekare' => 35,
                'oda_kapasitesi' => 3,
                'havuz_mevcut' => true,
                'havuz_tipi' => 'açık',
                'spa_mevcut' => true,
                'spa_hizmetleri' => json_encode(['masaj', 'sauna', 'hamam', 'jakuzi']),
                'spor_imkanlari' => json_encode(['fitness', 'tenis', 'golf', 'yüzme']),
                'cocuk_kulubu' => true,
                'cocuk_aktivite' => json_encode(['oyun alanı', 'animasyon', 'eğitici aktiviteler']),
                'denize_uzaklik' => 50,
                'merkeze_uzaklik' => 2000,
                'havaalani_uzaklik' => 25000,
                'transfer_hizmeti' => true,
                'otopark_mevcut' => true,
                'otopark_tipi' => 'ücretsiz',
                'yuksek_sezon_baslangic' => '2025-06-01',
                'yuksek_sezon_bitis' => '2025-09-30',
                'dusuk_sezon_baslangic' => '2025-10-01',
                'dusuk_sezon_bitis' => '2026-05-31',
                'sezonluk_fiyatlandirma' => true,
                'rezervasyon_sistemi' => 'online',
                'on_odeme_gerekli' => true,
                'on_odeme_orani' => 30.00,
                'iptal_politikasi' => 'Rezervasyon tarihinden 7 gün öncesine kadar ücretsiz iptal',
                'ai_kalite_puani' => 95,
                'ai_onerilen_fiyat' => 2500.00,
                'ai_hedef_musteri' => 'Lüks tatil arayan, yüksek gelirli aileler ve çiftler',
                'ai_pazarlama_stratejisi' => 'Premium segment odaklı, sosyal medya ve influencer marketing',
                'ai_analiz_tarihi' => now(),
                'ozel_ozellikler' => json_encode(['deniz manzarası', 'özel plaj', 'helikopter pisti']),
                'tesis_notlari' => '5 yıldızlı lüks resort otel, tüm hizmetler dahil',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ilan_id' => 2, // Örnek ilan ID
                'tesis_tipi' => 'pansiyon',
                'tesis_alt_tipi' => '3 Yıldız',
                'tesis_kategorisi' => 'aile',
                'toplam_oda_sayisi' => 25,
                'toplam_yatak_sayisi' => 50,
                'maksimum_kapasite' => 60,
                'minimum_kapasite' => 1,
                'yildiz_sayisi' => 3,
                'kalite_sinifi' => 'orta',
                'tesis_standarti' => 'yerel',
                'pansiyon_turu' => 'bb',
                'pansiyon_detaylari' => 'Sadece kahvaltı dahil',
                'yemek_hizmeti' => true,
                'yemek_turleri' => json_encode(['kahvaltı']),
                'oda_tipleri' => json_encode(['standart', 'aile']),
                'oda_ozellikleri' => json_encode(['klima', 'tv', 'wifi']),
                'oda_metrekare' => 25,
                'oda_kapasitesi' => 4,
                'havuz_mevcut' => false,
                'havuz_tipi' => null,
                'spa_mevcut' => false,
                'spa_hizmetleri' => null,
                'spor_imkanlari' => json_encode(['yürüyüş', 'bisiklet']),
                'cocuk_kulubu' => false,
                'cocuk_aktivite' => null,
                'denize_uzaklik' => 200,
                'merkeze_uzaklik' => 500,
                'havaalani_uzaklik' => 30000,
                'transfer_hizmeti' => false,
                'otopark_mevcut' => true,
                'otopark_tipi' => 'ücretsiz',
                'yuksek_sezon_baslangic' => '2025-06-01',
                'yuksek_sezon_bitis' => '2025-09-30',
                'dusuk_sezon_baslangic' => '2025-10-01',
                'dusuk_sezon_bitis' => '2026-05-31',
                'sezonluk_fiyatlandirma' => true,
                'rezervasyon_sistemi' => 'manuel',
                'on_odeme_gerekli' => false,
                'on_odeme_orani' => null,
                'iptal_politikasi' => 'Rezervasyon tarihinden 3 gün öncesine kadar ücretsiz iptal',
                'ai_kalite_puani' => 75,
                'ai_onerilen_fiyat' => 800.00,
                'ai_hedef_musteri' => 'Ekonomik tatil arayan aileler ve gençler',
                'ai_pazarlama_stratejisi' => 'Sosyal medya ve yerel reklamlar',
                'ai_analiz_tarihi' => now(),
                'ozel_ozellikler' => json_encode(['aile ortamı', 'samimi hizmet', 'uygun fiyat']),
                'tesis_notlari' => 'Aile pansiyonu, samimi ortam, ekonomik fiyatlar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($turistikTesisler as $tesis) {
            DB::table('turistik_tesis_detaylari')->insert($tesis);
        }

        $this->command->info(count($turistikTesisler).' adet turistik tesis detayı eklendi.');
    }

    /**
     * Turistik tesis özellikleri için örnek veriler
     */
    private function seedTuristikTesisOzellikleri(): void
    {
        $this->command->info('Turistik tesis özellikleri ekleniyor...');

        // İlk tesis için özellikler
        $ozellikler = [
            [
                'turistik_tesis_detay_id' => 1,
                'ozellik_adi' => 'Deniz Manzarası',
                'ozellik_degeri' => 'Evet',
                'ozellik_tipi' => 'konaklama',
                'onem_derecesi' => 'yuksek',
                'ai_analiz_agirligi' => 1.20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'turistik_tesis_detay_id' => 1,
                'ozellik_adi' => 'WiFi Hızı',
                'ozellik_degeri' => '100 Mbps',
                'ozellik_tipi' => 'hizmet',
                'onem_derecesi' => 'orta',
                'ai_analiz_agirligi' => 0.80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'turistik_tesis_detay_id' => 1,
                'ozellik_adi' => 'Restoran Sayısı',
                'ozellik_degeri' => '3',
                'ozellik_tipi' => 'yemek',
                'onem_derecesi' => 'yuksek',
                'ai_analiz_agirligi' => 1.10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'turistik_tesis_detay_id' => 2,
                'ozellik_adi' => 'Aile Odası',
                'ozellik_degeri' => 'Mevcut',
                'ozellik_tipi' => 'konaklama',
                'onem_derecesi' => 'yuksek',
                'ai_analiz_agirligi' => 1.15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'turistik_tesis_detay_id' => 2,
                'ozellik_adi' => 'Kahvaltı Çeşidi',
                'ozellik_degeri' => 'Türk Kahvaltısı',
                'ozellik_tipi' => 'yemek',
                'onem_derecesi' => 'orta',
                'ai_analiz_agirligi' => 0.90,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($ozellikler as $ozellik) {
            DB::table('turistik_tesis_ozellikleri')->insert($ozellik);
        }

        $this->command->info(count($ozellikler).' adet turistik tesis özelliği eklendi.');
    }

    /**
     * Rezervasyon takvimi için örnek veriler
     */
    private function seedRezervasyonTakvimi(): void
    {
        $this->command->info('Rezervasyon takvimi ekleniyor...');

        $rezervasyonlar = [];
        $baslangic = now()->startOfMonth();

        // 3 ay boyunca örnek rezervasyonlar
        for ($i = 0; $i < 90; $i++) {
            $tarih = $baslangic->copy()->addDays($i);
            $status = $this->getRandomStatus();
            $fiyat = $this->getRandomFiyat($status);

            $rezervasyonlar[] = [
                'turistik_tesis_detay_id' => 1,
                'tarih' => $tarih->format('Y-m-d'),
                'status' => $status,
                'oda_tipi' => 'standart',
                'fiyat' => $fiyat,
                'musait_oda_sayisi' => $status === 'available' ? rand(5, 20) : 0,
                'notlar' => $this->getRandomNotlar($status),
                'ozel_kosullar' => $this->getRandomKosullar($status),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Pansiyon için de örnek rezervasyonlar
        for ($i = 0; $i < 30; $i++) {
            $tarih = $baslangic->copy()->addDays($i);
            $status = $this->getRandomStatus();
            $fiyat = $this->getRandomFiyat($status, 'pansiyon');

            $rezervasyonlar[] = [
                'turistik_tesis_detay_id' => 2,
                'tarih' => $tarih->format('Y-m-d'),
                'status' => $status,
                'oda_tipi' => 'aile',
                'fiyat' => $fiyat,
                'musait_oda_sayisi' => $status === 'available' ? rand(2, 8) : 0,
                'notlar' => $this->getRandomNotlar($status),
                'ozel_kosullar' => $this->getRandomKosullar($status),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach ($rezervasyonlar as $rezervasyon) {
            DB::table('turistik_tesis_rezervasyon')->insert($rezervasyon);
        }

        $this->command->info(count($rezervasyonlar).' adet rezervasyon kaydı eklendi.');
    }

    /**
     * Rastgele status seçimi
     */
    private function getRandomStatus(): string
    {
        $statusList = ['available', 'available', 'available', 'option', 'booked', 'block'];

        return $statusList[array_rand($statusList)];
    }

    /**
     * Status'a göre rastgele fiyat
     */
    private function getRandomFiyat(string $status, string $tesisTipi = 'otel'): float
    {
        if ($tesisTipi === 'otel') {
            $basePrice = 2000.00;
        } else {
            $basePrice = 600.00;
        }

        switch ($status) {
            case 'available':
                return $basePrice + rand(-200, 300);
            case 'option':
                return $basePrice + rand(100, 500);
            case 'booked':
                return $basePrice + rand(300, 800);
            case 'block':
                return $basePrice + rand(500, 1000);
            default:
                return $basePrice;
        }
    }

    /**
     * Status'a göre rastgele notlar
     */
    private function getRandomNotlar(string $status): ?string
    {
        $notlar = [
            'available' => ['Müsait', 'Rezervasyon yapılabilir', 'Özel fiyat'],
            'option' => ['Opsiyon alındı', '24 saat içinde onay bekleniyor'],
            'booked' => ['Rezervasyon yapıldı', 'Dolu'],
            'block' => ['Bloke edildi', 'Bakım', 'Özel etkinlik'],
        ];

        $statusNotlari = $notlar[$status] ?? ['Bilgi yok'];

        return $statusNotlari[array_rand($statusNotlari)];
    }

    /**
     * Status'a göre rastgele koşullar
     */
    private function getRandomKosullar(string $status): ?string
    {
        if ($status === 'available') {
            return json_encode(['erken_rezervasyon_indirimi' => true, 'minimum_konaklama' => 1]);
        }

        if ($status === 'option') {
            return json_encode(['opsiyon_suresi' => 24, 'on_odeme_gerekli' => true]);
        }

        return null;
    }
}
