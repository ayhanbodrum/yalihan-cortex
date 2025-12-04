<?php

/**
 * Gerçek İlan Ekleme Script
 * 
 * KULLANIM:
 * php artisan tinker < GERCEK_ILAN_EKLEME_SCRIPT.php
 * 
 * VEYA:
 * php GERCEK_ILAN_EKLEME_SCRIPT.php (Laravel bootstrap ile)
 */

use App\Models\Kisi;
use App\Models\Ilan;
use Illuminate\Support\Facades\DB;

// 1. İLAN SAHİBİ OLUŞTUR/BUL - Ahmet Duran
$ahmetDuran = Kisi::firstOrCreate(
    ['telefon' => '05357339742'],
    [
        'ad' => 'Ahmet',
        'soyad' => 'Duran',
        'telefon' => '05357339742',
        'email' => null,
        'il_id' => 48,  // Muğla
        'ilce_id' => 341,  // Bodrum (varsayılan)
        'kisi_tipi' => 'bireysel',
        'kaynak' => 'manuel',
        'status' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]
);

echo "✅ İlan Sahibi: Ahmet Duran (ID: {$ahmetDuran->id})\n";

// 2. GÖREVLİ OLUŞTUR/BUL - Nahar Osman Bölük
$naharOsman = Kisi::firstOrCreate(
    ['telefon' => '05357456523'],
    [
        'ad' => 'Nahar Osman',
        'soyad' => 'Bölük',
        'telefon' => '05357456523',
        'email' => null,
        'il_id' => 48,
        'ilce_id' => 341,
        'kisi_tipi' => 'bireysel',
        'kaynak' => 'manuel',
        'gorevli_mi' => true,  // Görevli flag
        'status' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]
);

echo "✅ Görevli: Nahar Osman Bölük (ID: {$naharOsman->id})\n";

// 3. KATEGORİ ID'LERİNİ BUL
$konut = DB::table('ilan_kategorileri')
    ->where('adi', 'Konut')
    ->whereNull('parent_id')
    ->first();

$daire = DB::table('ilan_kategorileri')
    ->where('adi', 'Daire')
    ->where('parent_id', $konut->id)
    ->first();

$satilik = DB::table('ilan_kategori_yayin_tipleri')
    ->where('yayin_tipi', 'Satılık')
    ->first();

echo "Kategori - Konut ID: {$konut->id}\n";
echo "Alt Kategori - Daire ID: {$daire->id}\n";
echo "Yayın Tipi - Satılık ID: {$satilik->id}\n";

// 4. LOKASYON ID'LERİ
$mugla = DB::table('iller')->where('adi', 'Muğla')->first();
$bodrum = DB::table('ilceler')->where('adi', 'Bodrum')->where('il_id', $mugla->id)->first();
$yalıkavak = DB::table('mahalleler')
    ->where('adi', 'like', '%Yalıkavak%')
    ->where('ilce_id', $bodrum->id)
    ->first();

echo "İl - Muğla ID: {$mugla->id}\n";
echo "İlçe - Bodrum ID: {$bodrum->id}\n";
echo "Mahalle - Yalıkavak ID: " . ($yalıkavak->id ?? 'BULUNAMADI') . "\n";

// 5. İLAN OLUŞTUR
$ilan = Ilan::create([
    // Temel Bilgiler
    'baslik' => 'Yalıkavak Deniz Manzaralı Lüks Daire - Ülküler Sitesi',
    'aciklama' => 'Yalıkavak\'ın prestijli Ülküler Sitesi\'nde, deniz manzaralı 3+1 lüks daire. 
    
🏡 Daire Özellikleri:
• 145 m² brüt alan, 125 m² net kullanım alanı
• 3 yatak odası + 1 salon
• 2 banyo, 1 balkon
• 4. kat (Toplam 6 katlı bina)
• 2 yıllık modern bina

🏊 Site Özellikleri:
• Ülküler Sitesi - Prestijli lokasyon
• Açık yüzme havuzu
• Kapalı otopark
• 7/24 güvenlik
• Asansör

🌊 Lokasyon Avantajları:
• Deniz manzarası
• Yalıkavak Marina\'ya 2 km
• Çarşıya yürüme mesafesi
• Denize 800 metre

💎 Donanım:
• Kombi (Doğalgaz) ısıtma
• Tüm odalarda klima
• Modern mutfak
• Laminat parke zemin

📞 İletişim: Ahmet Duran
👷 Görevli: Nahar Osman Bölük',
    
    'fiyat' => 5500000,
    'para_birimi' => 'TRY',
    'fiyat_text' => 'Beş Milyon Beş Yüz Bin Türk Lirası',
    
    // Kategori
    'kategori_id' => $konut->id,
    'alt_kategori_id' => $daire->id,
    'yayin_tipi_id' => $satilik->id,
    
    // Lokasyon
    'il_id' => $mugla->id,
    'ilce_id' => $bodrum->id,
    'mahalle_id' => $yalıkavak->id ?? null,
    'adres' => 'Ülküler Sitesi, Yalıkavak, Bodrum',
    'enlem' => 37.1676,
    'boylam' => 27.2035,
    
    // Daire Özel Alanlar
    'oda_sayisi' => 3,
    'salon_sayisi' => 1,
    'brut_alan_m2' => 145.00,
    'net_alan_m2' => 125.00,
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
    
    // İlan Sahibi ve Görevli
    'ilan_sahibi_kisi_id' => $ahmetDuran->id,
    'ilgili_kisi_id' => $naharOsman->id,  // Görevli
    'danisman_id' => 1,  // Varsayılan danışman
    
    // Meta
    'status' => 1,  // Aktif
    'aktif_mi' => 1,
    'onay_durumu' => 'onaylandi',
    'tapu_durumu' => 'Kat Mülkiyeti',
    
    // AI Flags
    'ai_generated_description' => false,  // Manuel yazıldı
    'ai_confidence_score' => null,
    
    // Tarihler
    'ilan_tarihi' => now(),
    'created_at' => now(),
    'updated_at' => now()
]);

echo "✅ İlan Oluşturuldu! ID: {$ilan->id}\n";
echo "🔗 URL: http://127.0.0.1:8000/admin/ilanlar/{$ilan->id}\n";

exit;
EOF
