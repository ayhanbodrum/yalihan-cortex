# Gemini AI Fotoğraf Sıralama Sistemi - Uygulama Planı

**Tarih:** 2 Aralık 2025  
**Versiyon:** 1.0.0  
**Durum:** 📋 PLANLAMA  
**Yalıhan Bekçi Standardı:** YB-AI-PHOTO-SEQ-2025-12-02  
**Context7 Uyumlu:** %100  
**Gemini AI Önerisi:** FOTOĞRAF SIRALAMA OPTİMİZASYONU

---

## 🎯 VİZYON

Gemini AI önerisi: **AI destekli fotoğraf sıralama** ile satış performansını artırmak.

### **2 Ana Özellik:**

1. **Otomatik Kapak Fotoğrafı Önerisi** (Auto Featured Photo)
2. **Satış Stratejisine Göre Sıralama** (Sales-Optimized Sequencing)

---

## 📋 GÖREV 1: OTOMATIK KAPAK FOTOĞRAFI ÖNERİSİ

### **Amaç:**
İlan oluşturulurken kapak fotoğrafı seçilmemişse, AI en iyi fotoğrafı seçmeli.

### **Mantık:**
1. Dosya adı analizi (`deniz_manzarasi_kapak.jpg`)
2. Çözünürlük kontrolü (en yüksek)
3. Dosya boyutu (kalite göstergesi)
4. Gemini Vision API (gelecek)

### **Teknik Uygulama:**

**Dosya:** `app/Services/AI/YalihanCortex.php`

```php
/**
 * Otomatik kapak fotoğrafı önerisi
 * Gemini AI Önerisi: En kaliteli fotoğrafı seç
 *
 * @param Ilan $ilan
 * @return int|null Önerilen fotoğraf ID'si
 */
public function suggestFeaturedPhoto(Ilan $ilan): ?int
{
    $fotograflar = $ilan->fotograflar;
    
    if ($fotograflar->isEmpty()) {
        return null;
    }
    
    // Zaten kapak seçilmişse atla
    $mevcutKapak = $fotograflar->where('kapak_fotografi', true)->first();
    if ($mevcutKapak) {
        return null;
    }
    
    // 1. Dosya adında "kapak" geçen var mı?
    $kapakAday = $fotograflar->filter(function ($foto) {
        return stripos($foto->dosya_yolu, 'kapak') !== false ||
               stripos($foto->dosya_yolu, 'cover') !== false ||
               stripos($foto->dosya_yolu, 'featured') !== false;
    })->first();
    
    if ($kapakAday) {
        return $kapakAday->id;
    }
    
    // 2. En yüksek sıra numaralı (en öne eklenen)
    $ilkFoto = $fotograflar->sortBy('sira')->first();
    
    if ($ilkFoto) {
        return $ilkFoto->id;
    }
    
    // 3. Fallback: İlk eklenen
    return $fotograflar->first()->id ?? null;
}
```

**Controller Entegrasyonu:** `IlanController@store`

```php
// İlan kaydedildikten sonra
if ($ilan->fotograflar->count() > 0) {
    $cortex = app(YalihanCortex::class);
    $onerilenKapak = $cortex->suggestFeaturedPhoto($ilan);
    
    if ($onerilenKapak) {
        return redirect()->route('admin.ilanlar.show', $ilan)
            ->with('success', 'İlan başarıyla oluşturuldu.')
            ->with('ai_suggestion', [
                'type' => 'featured_photo',
                'photo_id' => $onerilenKapak,
                'message' => 'AI tarafından kapak fotoğrafı önerildi. Onaylamak ister misiniz?'
            ]);
    }
}
```

---

## 📋 GÖREV 2: SATIŞ STRATEJİSİNE GÖRE SIRALAMA

### **Amaç:**
Kategori bazlı optimal fotoğraf sıralaması.

### **Strateji:**

**Villa:**
1. Havuz fotoğrafı
2. Salon/Living room
3. Manzara
4. Yatak odaları
5. Dış cephe

**Arsa:**
1. Konum haritası
2. Tapu/İmar belgesi
3. Yol cephesi
4. Manzara
5. Çevre görünümü

**Daire:**
1. Salon
2. Mutfak
3. Yatak odası
4. Balkon/Manzara
5. Bina dış görünümü

### **Teknik Uygulama:**

**Dosya:** `app/Services/AI/YalihanCortex.php`

```php
/**
 * Kategori bazlı optimal fotoğraf sıralaması öner
 * Gemini AI Önerisi: Satış stratejisine göre sıralama
 *
 * @param Ilan $ilan
 * @return array Önerilen sıralama ['photo_id' => display_order]
 */
public function optimizePhotoSequence(Ilan $ilan): array
{
    $fotograflar = $ilan->fotograflar;
    
    if ($fotograflar->isEmpty()) {
        return [];
    }
    
    $kategoriSlug = strtolower($ilan->altKategori->slug ?? $ilan->anaKategori->slug ?? '');
    
    // Kategori bazlı anahtar kelimeler
    $strategyMap = [
        'villa' => ['havuz', 'pool', 'salon', 'living', 'manzara', 'view', 'yatak', 'bedroom'],
        'arsa' => ['konum', 'location', 'harita', 'map', 'tapu', 'yol', 'road', 'cephe'],
        'daire' => ['salon', 'living', 'mutfak', 'kitchen', 'yatak', 'bedroom', 'balkon', 'manzara'],
    ];
    
    $keywords = $strategyMap[$kategoriSlug] ?? $strategyMap['daire'];
    
    // Fotoğrafları skorla
    $scored = $fotograflar->map(function ($foto) use ($keywords) {
        $score = 0;
        $dosyaAdi = strtolower($foto->dosya_yolu);
        
        foreach ($keywords as $index => $keyword) {
            if (stripos($dosyaAdi, $keyword) !== false) {
                // İlk keyword'ler daha yüksek skor
                $score += (count($keywords) - $index) * 10;
            }
        }
        
        return [
            'id' => $foto->id,
            'score' => $score,
            'current_order' => $foto->sira ?? 999,
        ];
    })->sortByDesc('score');
    
    // Yeni sıralama oluştur
    $newSequence = [];
    $order = 1;
    
    foreach ($scored as $item) {
        $newSequence[$item['id']] = $order++;
    }
    
    return $newSequence;
}
```

**Frontend Butonu:** İlan düzenleme sayfasında

```blade
<!-- AI Sıralama Butonu -->
<button onclick="applyAISequence()" 
        class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:shadow-xl">
    <svg class="w-4 h-4 inline mr-2">...</svg>
    AI Sırası Uygula
</button>

<script>
function applyAISequence() {
    fetch('/api/admin/ilanlar/{{ $ilan->id }}/ai-photo-sequence', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Sıralamayı uygula
            data.sequence.forEach((photoId, index) => {
                updatePhotoOrder(photoId, index + 1);
            });
            showToast('✅ AI sıralaması uygulandı!');
        }
    });
}
</script>
```

---

## 🔧 MEVCUT SISTEM KONTROL

### **Gerekli Alanlar:**

**IlanFotografi Tablosu:**
- ✅ `sira` veya `display_order` (sıralama)
- ✅ `kapak_fotografi` (is_featured)
- ✅ `dosya_yolu` (file_path)

**API Endpoint'ler:**
- ✅ `IlanController::updatePhotoSequence` (mevcut)
- 🆕 `IlanController::aiPhotoSequence` (eklenecek)

**YalihanCortex:**
- ✅ Mevcut
- 🆕 `suggestFeaturedPhoto()` eklenecek
- 🆕 `optimizePhotoSequence()` eklenecek

---

## 📊 UYGULAMA PLANI

### **Faz 1: Temel Altyapı** (1 saat)
1. ✅ YalihanCortex metodları ekle
2. ✅ API endpoint ekle
3. ✅ Route tanımla

### **Faz 2: Frontend Entegrasyonu** (1 saat)
1. ✅ "AI Sırası Uygula" butonu ekle
2. ✅ AJAX çağrısı yap
3. ✅ Toast notification göster

### **Faz 3: Gemini Vision API** (Gelecek - Opsiyonel)
1. Görsel kalite analizi
2. Nesne tespiti (havuz, deniz, salon)
3. En iyi açı/kompozisyon seçimi

---

## 🎯 BEKLENEN SONUÇLAR

### **Senaryo 1: Otomatik Kapak**
```
Danışman: 5 fotoğraf yükledi, kapak seçmedi
AI: "havuz_manzara.jpg" seçildi
Sonuç: ✅ Otomatik kapak önerisi
```

### **Senaryo 2: AI Sıralama**
```
Kategori: Villa
Mevcut Sıra: foto1.jpg, foto2.jpg, foto3.jpg
AI Önerisi: havuz.jpg, salon.jpg, manzara.jpg
Danışman: "AI Sırası Uygula" butonuna tıklar
Sonuç: ✅ Satış odaklı sıralama
```

---

## 📝 CONTEXT7 UYUMU

### ✅ Uyumlu:
- `sira` veya `display_order` kullanımı
- `kapak_fotografi` veya `is_featured`
- Tailwind CSS ile UI
- Vanilla JavaScript (heavy library yok)

### ❌ Forbidden Pattern Yok:
- `order` → `display_order` veya `sira` ✅
- `enabled` kullanılmıyor ✅
- Bootstrap kullanılmıyor ✅

---

## 🚀 UYGULAMA KARARI

**Gemini AI Önerisi:** Fotoğraf sıralama optimizasyonu

**Durum:** 📋 PLANLANDI

**Uygulama Zamanı:** Yarın veya sonraki sprint

**Öncelik:** Orta (Mevcut sistem çalışıyor, bu iyileştirme)

---

**Şimdi uygulamaya geçelim mi yoksa önce bugünkü işleri tamamlayalım mı?** 🎯

**Seçenekler:**
1. ✅ Bugünkü işleri bitir (dökümanları düzenle, commit hazırla)
2. 🚀 AI Fotoğraf Sıralama'yı şimdi uygula
3. 🛑 Yarına bırak

Hangisi? 😊

