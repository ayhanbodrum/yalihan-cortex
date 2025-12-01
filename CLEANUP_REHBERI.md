# 🧹 YALİHAN EMLAK TEMİZLİK RAPORU

**25 Kasım 2025**

---

## 📊 MEVCUT PROJE DURUMU

| Klasör              | Boyut      | Durum                                 |
| ------------------- | ---------- | ------------------------------------- |
| **archive/**        | 228 KB     | Silinebilir (eski raporlar)           |
| **docs/archive/**   | 4.5 MB     | Silinebilir (geçmiş dökümantasyon)    |
| **screenshots/**    | 23 MB      | Silinebilir (test görselleri)         |
| **.yalihan-bekci/** | 3.7 MB     | Silinebilir (IDE cache)               |
| **.vscode/**        | 71 MB      | Silinebilir (IDE settings)            |
| **.cursor/**        | 26 MB      | Silinebilir (IDE settings)            |
| **vendor/**         | 321 MB     | Rebuild edilebilir (PHP dependencies) |
| **node_modules/**   | 217 MB     | Rebuild edilebilir (NPM dependencies) |
| **TOPLAM**          | **928 MB** | —                                     |

---

## 🎯 TEMİZLİK SEÇENEKLERI

### 1️⃣ HIZLI TEMİZLİK (Önerilen - 4.7 MB)

**Risk Seviyesi:** 🟢 DÜŞÜK  
**Silinecekler:**

- `archive/` (228 KB) - Eski raporlar
- `docs/archive/` (4.5 MB) - Geçmiş dokümanlar

**Komut:**

```bash
./scripts/cleanup/INTERACTIVE_CLEANUP.sh
# Seç: 1
```

**Avantajlar:**

- ✅ Hiçbir risk yok
- ✅ Git geçmişi korunur
- ✅ Projede çalışan hiçbir şey etkilenmez
- ✅ Yedek otomatik alınır

**Dezavantajlar:**

- ❌ Az yer açılır (4.7 MB)

---

### 2️⃣ ORTA TEMİZLİK (Dengeli - 28.7 MB)

**Risk Seviyesi:** 🟡 ORTA  
**Silinecekler:**

- Hızlı temizlik +
- `screenshots/` (23 MB) - Test görselleri
- `.yalihan-bekci/` (3.7 MB) - IDE cache

**Komut:**

```bash
./scripts/cleanup/INTERACTIVE_CLEANUP.sh
# Seç: 2
```

**Avantajlar:**

- ✅ Daha fazla yer açılır (28.7 MB)
- ✅ IDE cache'i yeniden oluşturulur
- ✅ Test görselleri kaldırılır
- ✅ Hala güvenli

**Dezavantajlar:**

- ❌ IDE startup yavaşlayabilir (cache yeniden oluşturulur)
- ⚠️ Screenshots tamamen silinir

---

### 3️⃣ İLERİ TEMİZLİK (Agresif - 123.7 MB)

**Risk Seviyesi:** 🔴 YÜKSEK  
**Silinecekler:**

- Orta temizlik +
- `.vscode/` (71 MB) - VS Code settings
- `.cursor/` (26 MB) - Cursor settings
- Git geçmiş optimizasyonu (~26 MB)

**Komut:**

```bash
./scripts/cleanup/INTERACTIVE_CLEANUP.sh
# Seç: 3
# Onayla: evet
```

**Avantajlar:**

- ✅ Çok fazla yer açılır (123.7 MB)
- ✅ IDE ayarları temizlenir
- ✅ Git geçmişi optimize edilir

**Dezavantajlar:**

- ❌ IDE ayarları sıfırlanır (yeniden konfigüre gerekir)
- ⚠️ Git geçmişinde değişiklik
- ⚠️ Extensions yeniden yüklenmesi gerekebilir

---

### 4️⃣ RADICAL (Maksimum - 538 MB)

**Risk Seviyesi:** 🚨 ÇOĞU KÜTÜPHANE SİLİNECEK  
**Silinecekler:**

- İleri temizlik +
- `vendor/` (321 MB) - PHP dependencies
- `node_modules/` (217 MB) - NPM dependencies

**Komut:**

```bash
./scripts/cleanup/INTERACTIVE_CLEANUP.sh
# Seç: 4
# Onayla: evet
```

**Not:** Script otomatik olarak yeniden kuracak (20-30 dakika)

**Avantajlar:**

- ✅ Projede çalışmayan hiçbir dosya kalmaz
- ✅ Maksimum yer açılır (538 MB)
- ✅ Temiz başlangıç

**Dezavantajler:**

- ❌ 20-30 dakika sürer
- ⚠️ İnternet bağlantısı gerekir
- ⚠️ Tüm IDE ayarları sıfırlanır

---

### 5️⃣ ÖZEL SEÇİM (Manuel)

**Risk Seviyesi:** 📋 KENDİ KONTROLÜNDEDE

Hangi klasörleri silmek istediğinizi seçin.

```bash
./scripts/cleanup/INTERACTIVE_CLEANUP.sh
# Seç: 5
# Her item için e/h cevabı ver
```

---

## 🛡️ GÜVENLİK & YEDEKLEME

### Otomatik Yedekleme

Temizlik işleminden önce `backups/` klasöründe `.tar.gz` dosyası oluşturulur:

```
backups/
├── backup-20251125-143022.tar.gz  (hızlı temizlik)
├── backup-20251125-144156.tar.gz  (orta temizlik)
└── backup-full-20251125-145301.tar.gz  (radical)
```

### Yedekten Geri Alma

```bash
# Listeyi gör
ls -lh backups/

# Geri al
tar -xzf backups/backup-XXXXX.tar.gz -C /Users/macbookpro/Projects/yalihanai/
```

---

## 📋 YAPILMASI GEREKENLER

### Temizlik Öncesi

- [ ] Çalışan sunucuyu kapat
- [ ] Tüm değişiklikleri commit et
- [ ] Güvenli bir yere yedek al (isteğe bağlı)

### Temizlik Sonrası

**Hızlı / Orta seçildiyse:**

- Hiçbir şey yapmaya gerek yok

**İleri seçildiyse:**

- IDE'yi yeniden aç
- Extensions yeniden yüklensin
- Settings tekrar konfigüre et

**Radical seçildiyse:**

- 20-30 dakika bekle (kurulum devam ediyor)
- Kurulum bitince IDE'yi yeniden aç
- `npm run dev` ile Vite sunucusunu başlat
- `php artisan serve --port=8002` ile Laravel sunucusunu başlat

---

## ⚠️ UYARILAR

### Hiçbir Zaman Silme

- ✅ `app/` - Proje kodu
- ✅ `resources/` - Blade templates, CSS
- ✅ `config/` - Konfigürasyon
- ✅ `routes/` - Route tanımları
- ✅ `database/` - Migration'lar
- ✅ `.env` - Ortam değişkenleri
- ✅ `composer.lock` & `package-lock.json`
- ✅ `.git` - (İleri temizlikte optimize edilir, kısmen silinmez)

### IDE Ayarlarını Kaybetme Riski

- `.vscode/` silersen: VS Code ayarları sıfırlanır
- `.cursor/` silersen: Cursor ayarları sıfırlanır
- Extensions yeniden yüklenmeleri gerekebilir

---

## 🚀 HIZLI BAŞLAMA

```bash
# 1. Temizlik scriptini çalıştır
cd /Users/macbookpro/Projects/yalihanai
./scripts/cleanup/INTERACTIVE_CLEANUP.sh

# 2. Seç (Önerilen: 1 - Hızlı temizlik)
# Seçiminizi yapın (0-6): 1

# 3. Tamamlanmasını bekle

# 4. Boyutun azaldığını kontrol et
du -sh .
```

---

## 📞 SORULAR & CEVAPLAR

**S: Hangi seçeneği seçmeliyim?**
C: `1 - HIZLI TEMİZLİK` başla. Sonra gerekirse `2` veya `3` yap.

**S: Yedekleri geri alabilir miyim?**
C: Evet, `backups/` klasöründe .tar.gz dosyaları bulunur.

**S: `vendor/` neden bu kadar büyük?**
C: Laravel ekosistemi için 1000+ PHP paketi içerir.

**S: `node_modules/` silebilir miyim?**
C: Evet, `npm install` ile yeniden kurulur (~5 dakika).

**S: Proje çalışmaz mı?**
C: Radical'i seçersen script otomatik kuracak.

---

## 📊 TEMIZLIK ÖNCESİ & SONRASI

### Scenario 1: HIZLI TEMİZLİK

```
ÖNCE : 928 MB
SONRA: 923 MB (4.7 MB tasarruf)
```

### Scenario 2: ORTA TEMİZLİK

```
ÖNCE : 928 MB
SONRA: 899 MB (28.7 MB tasarruf)
```

### Scenario 3: İLERİ TEMİZLİK

```
ÖNCE : 928 MB
SONRA: 804 MB (123.7 MB tasarruf)
```

### Scenario 4: RADICAL

```
ÖNCE : 928 MB
SONRA: 390 MB (538 MB tasarruf) - Kurulum tamamlanır
```

---

**Hazırsın! 🚀**

Temizliği başlatmak için:

```bash
./scripts/cleanup/INTERACTIVE_CLEANUP.sh
```
