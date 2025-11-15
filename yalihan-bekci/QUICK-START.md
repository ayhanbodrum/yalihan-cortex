# ⚡ TestSprite Hızlı Başlangıç

## 🚀 3 Adımda Başlat

### **1. Node Kurulumu (İlk seferde)**

```bash
cd testsprite/server
npm install
cd ../..
```

### **2. Otomatik Öğrenme Aktif Et**

```bash
# Context7 kurallarını öğren
php artisan testsprite:auto-learn
```

### **3. Test Çalıştır**

**Seçenek A - Bash Script:**

```bash
./testsprite/test-run.sh
```

**Seçenek B - Laravel Artisan:**

```bash
php artisan testsprite:run
```

**Seçenek C - Manuel:**

```bash
cd testsprite/server
node index.js
# Başka terminalde:
curl http://localhost:3333/context7/rules
```

---

## 🧠 OTOMATIK ÖĞRENME

### **Cursor MCP Entegrasyonu:**

TestSprite otomatik olarak `.cursor/mcp.json` ile entegre!

**Kullanım:**

1. Cursor açılır → TestSprite otomatik başlar
2. Kod yazarken → Context7 kuralları kontrol eder
3. Yasak kullanım tespit edilir → Uyarı verir

### **Zamanlanmış Çalışma:**

```bash
# Scheduler çalışıyor mu kontrol et:
php artisan schedule:list

# Manuel tetikle:
php artisan schedule:run
```

**Otomatik görevler:**

- **Her gün 03:00:** Kuralları yeniden öğren
- **Her 6 saat:** Testleri çalıştır
- **Her Pazar 02:00:** Context7 compliance check

---

## 📊 SONUÇLARI GÖRÜNTÜLE

```bash
# Son test sonuçları:
cat testsprite_tests/test_report.md

# Öğrenilmiş kurallar:
cat testsprite/knowledge/context7-rules.json

# Pattern'ler:
cat testsprite/knowledge/patterns.json

# API ile:
curl http://localhost:3333/context7/rules
curl http://localhost:3333/patterns/common
```

---

## 🎯 CURSOR'DA KULLANIM

TestSprite Cursor açıldığında otomatik çalışır:

1. **Kod yazarken:**
    - Yasaklı kullanım → Altı kırmızı çizilir
    - Hover → Öneri gösterilir

2. **Kaydettiğinde:**
    - Otomatik validate eder
    - Context7 ihlal var mı kontrol eder

3. **Her 6 saatte:**
    - Tüm proje taranır
    - Öğrenilmiş pattern'ler güncellenir

---

## ⚙️ AYARLAR

```bash
# .env'e ekle:
TESTSPRITE_AUTO_LEARN=true
TESTSPRITE_AUTO_RUN=true
TESTSPRITE_INTERVAL=21600  # 6 saat (saniye)
```

**Hepsi bu!** 🎉
