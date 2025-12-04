<?php

/**
 * Gerçek İlan Ekleme Script
 * Çalıştırma: php artisan tinker < ilan-ekle.php
 */

use App\Models\Kisi;
use App\Models\Ilan;
use Illuminate\Support\Facades\DB;

echo "🚀 İlan Ekleme Başlıyor...\n\n";

// 1. KIŞILERI OLUŞTUR
echo "1️⃣ Kişiler oluşturuluyor...\n";

$ahmetDuran = Kisi::firstOrCreate(
    ['telefon' => '05357339742'],
    [
        'ad' => 'Ahmet',
        'soyad' => 'Duran',
        'telefon' => '05357339742',
        'il_id' => 48,
        'kisi_tipi' => 'bireysel',
        'status' => 1
    ]
);
echo "   ✅ İlan Sahibi: Ahmet Duran (ID: {$ahmetDuran->id})\n";

$naharOsman = Kisi::firstOrCreate(
    ['telefon' => '05357456523'],
    [
        'ad' => 'Nahar Osman',
        'soyad' => 'Bölük',
        'telefon' => '05357456523',
        'il_id' => 48,
        'kisi_tipi' => 'bireysel',
        'status' => 1
    ]
);
echo "   ✅ Görevli: Nahar Osman Bölük (ID: {$naharOsman->id})\n\n";

// 2. İLAN OLUŞTUR
echo "2️⃣ İlan oluşturuluyor...\n";

$ilan = Ilan::create([
    'baslik' => 'Yalıkavak Deniz Manzaralı Lüks Daire - Ülküler Sitesi',
    'aciklama' => 'Yalıkavak\'ın prestijli Ülküler Sitesi\'nde, deniz manzaralı 3+1 lüks daire. 145 m² brüt, 125 m² net kullanım alanı. Site havuzu, asansör, kapalı otopark. 2 yıllık modern bina. Denize 800m, marina 2km. Görevli: Nahar Osman Bölük',
    'fiyat' => 5500000,
    'para_birimi' => 'TRY',
    'fiyat_text' => 'Beş Milyon Beş Yüz Bin Türk Lirası',
    
    // Kategori
    'kategori_id' => 1,
    'alt_kategori_id' => 5,
    'yayin_tipi_id' => 1,
    
    // Lokasyon (Muğla > Bodrum > Yalıkavak)
    'il_id' => 48,
    'ilce_id' => 341,
    'mahalle_id' => null,  // Manuel set edilecek
    'adres' => 'Ülküler Sitesi, Yalıkavak, Bodrum',
    'enlem' => 37.1676,
    'boylam' => 27.2035,
    
    // Daire Detaylar
    'oda_sayisi' => 3,
    'salon_sayisi' => 1,
    'brut_alan_m2' => 145,
    'net_alan_m2' => 125,
    'banyo_sayisi' => 2,
    'balkon_sayisi' => 1,
    'kat_numarasi' => 4,
    'bina_kat_sayisi' => 6,
    'bina_yasi' => 2,
    'isitma_tipi' => 'Kombi (Doğalgaz)',
    'site_icinde' => 1,
    'site_adi' => 'Ülküler Sitesi',
    'asansor' => 1,
    'otopark' => 1,
    'balkon' => 1,
    
    // İlan Sahibi
    'ilan_sahibi_kisi_id' => $ahmetDuran->id,
    'ilgili_kisi_id' => $naharOsman->id,
    'danisman_id' => 1,
    
    // Durum
    'status' => 1,
    'aktif_mi' => 1,
    'onay_durumu' => 'onaylandi',
    
    // Tarihler
    'ilan_tarihi' => now(),
    'created_at' => now(),
    'updated_at' => now()
]);

echo "   ✅ İlan Oluşturuldu! (ID: {$ilan->id})\n\n";

// 3. ÖZET GÖSTER
echo "╔═══════════════════════════════════════════════════════╗\n";
echo "║     İLAN BAŞARIYLA OLUŞTURULDU!                       ║\n";
echo "╠═══════════════════════════════════════════════════════╣\n";
echo "║                                                        ║\n";
echo "║ 🆔 İlan ID: {$ilan->id}                               \n";
echo "║ 🏠 Başlık: Yalıkavak Deniz Manzaralı...               ║\n";
echo "║ 💰 Fiyat: ₺5.500.000 TRY                              ║\n";
echo "║ 📍 Lokasyon: Yalıkavak, Bodrum, Muğla                 ║\n";
echo "║ 📏 Alan: 145 m² (Brüt) / 125 m² (Net)                ║\n";
echo "║ 🛏️ Oda: 3+1                                           ║\n";
echo "║ 🏢 Site: Ülküler Sitesi                               ║\n";
echo "║                                                        ║\n";
echo "║ 👤 Malik: Ahmet Duran (0535-733-9742)                 ║\n";
echo "║ 👷 Görevli: Nahar Osman Bölük (0535-745-6523)        ║\n";
echo "║                                                        ║\n";
echo "║ 🔗 Admin: http://127.0.0.1:8000/admin/ilanlar/{$ilan->id}  ║\n";
echo "║ 🌐 Frontend: http://127.0.0.1:8000/ilanlar/{$ilan->id}    ║\n";
echo "║                                                        ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

echo "📊 Sonraki Adımlar:\n";
echo "   □ Fotoğraf ekle (Admin panel'den)\n";
echo "   □ Özellikleri seç (Deniz manzarası, Havuz, vs.)\n";
echo "   □ Reverse match çalıştır (uygun müşteriler)\n\n";

echo "✅ İlan ekleme tamamlandı!\n";

